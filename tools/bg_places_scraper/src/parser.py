"""Parse index HTML and collect place detail URLs (not letter/pagination/nav links)."""

import re
from urllib.parse import unquote, urljoin, urlparse

from bs4 import BeautifulSoup

from src import config

# Meta description: "гр. Име, ЕКАТТЕ 12345, област Област, община Община, ПК: …"
_META_DESC_RE = re.compile(
    r"^(?P<prefix>\S+)\s+(?P<name>[^,]+),\s*ЕКАТТЕ\s*(?P<ekatte>\d+),\s*област\s*(?P<region>[^,]+),\s*община\s*(?P<muni>[^,(]+)",
    re.UNICODE,
)

# Title: "гр. Име - област Област, община Община, ЕКАТТЕ 12345"
_TITLE_RE = re.compile(
    r"^(?P<prefix>\S+)\s+(?P<name>[^-]+?)\s*-\s*област\s*(?P<region>[^,]+),\s*община\s*(?P<muni>[^,]+),\s*ЕКАТТЕ\s*(?P<ekatte>\d+)",
    re.UNICODE,
)

# Map Bulgarian settlement-type prefix (from site) → Laravel ``places.type``.
_BG_TYPE_TO_PLACE_TYPE: dict[str, str] = {
    "гр.": "city",
    "град": "city",
    "с.": "village",
    "село": "village",
    "к.к.": "resort",
    "к.": "resort",
    "курорт": "resort",
    "с.к.": "village",
    "п.": "village",
    "ст.": "village",
    "ман.": "village",
    "гск.": "village",
}

_FORMATION_MUNI_REGION_RE = re.compile(
    r"общ\.\s*(?P<muni>[^,|]+),\s*обл\.\s*(?P<region>[^,|]+)",
    re.UNICODE,
)


def parse_letter_subindex_urls(html: str) -> list[str]:
    """Collect absolute URLs for ``/а-я/<one letter>`` sub-indexes linked from the root index."""
    if not html.strip():
        return []
    soup = BeautifulSoup(html, "lxml")
    found: list[str] = []
    seen: set[str] = set()
    for a in soup.select("a[href]"):
        href = (a.get("href") or "").strip()
        if not href or href.startswith("#"):
            continue
        absolute = urljoin(config.BASE_URL + "/", href)
        path = unquote(urlparse(absolute).path)
        parts = [p for p in path.split("/") if p]
        if len(parts) != 2 or parts[0] != "а-я" or len(parts[1]) != 1:
            continue
        if absolute not in seen:
            seen.add(absolute)
            found.append(absolute)
    return found


def _is_place_detail_url(absolute_url: str) -> bool:
    """True for /област-…/община-…/… settlement pages on ekatte.com."""
    if not absolute_url.startswith(config.BASE_URL):
        return False
    path = unquote(urlparse(absolute_url).path)
    parts = [p for p in path.split("/") if p]
    if len(parts) != 3:
        return False
    return parts[0].startswith("област-") and parts[1].startswith("община-")


def parse(html: str) -> list[dict]:
    """Extract ``{url, label}`` entries for each place detail link on the index page."""
    if not html.strip():
        return []
    soup = BeautifulSoup(html, "lxml")
    rows: list[dict] = []
    for a in soup.select("a[href]"):
        href = (a.get("href") or "").strip()
        if not href or href.startswith("#"):
            continue
        absolute = urljoin(config.BASE_URL + "/", href)
        if not _is_place_detail_url(absolute):
            continue
        label = a.get_text(strip=True) or None
        rows.append({"url": absolute, "label": label})
    return rows


def parse_place_detail(html: str, source_url: str, index_label: str | None = None) -> dict:
    """
    Parse one place detail page HTML into Laravel-oriented fields.

    Uses ``<meta name="description">`` first, then ``<title>``. Unknown BG type
    prefixes leave ``type`` as ``null`` and add a warning (no guessing).
    """
    warnings: list[str] = []
    soup = BeautifulSoup(html, "lxml")
    meta = soup.find("meta", attrs={"name": "description"})
    title_el = soup.find("title")
    meta_content = (meta.get("content") or "").strip() if meta else ""
    title_text = title_el.get_text(strip=True) if title_el else ""

    match = _META_DESC_RE.match(meta_content) if meta_content else None
    if not match and title_text:
        match = _TITLE_RE.match(title_text)
        if match:
            warnings.append("Used <title> fallback; <meta name=\"description\"> missing or unparsed.")
    if not match:
        warnings.append("Could not parse structured fields from meta description or title.")
        return {
            "source_url": source_url,
            "index_label": index_label,
            "name": None,
            "ekatte_code": None,
            "municipality_name": None,
            "region_name": None,
            "type": None,
            "extraction_warnings": warnings,
        }

    prefix = match.group("prefix").strip()
    name = match.group("name").strip()
    ekatte = match.group("ekatte").strip()
    region = match.group("region").strip()
    muni = match.group("muni").strip()
    place_type = _BG_TYPE_TO_PLACE_TYPE.get(prefix)
    if place_type is None:
        warnings.append(f"Unknown settlement-type prefix {prefix!r}; type left unset (not invented).")

    return {
        "source_url": source_url,
        "index_label": index_label,
        "name": name,
        "ekatte_code": ekatte,
        "municipality_name": muni,
        "region_name": region,
        "type": place_type,
        "extraction_warnings": warnings,
    }


def parse_settlement_formations(html: str, source_url: str) -> list[dict]:
    """
    Parse rows from /селищни-образувания.

    This source has no per-row detail links, so each row gets a synthetic
    stable ``source_url`` fragment by EKATTE code.
    """
    if not html.strip():
        return []

    soup = BeautifulSoup(html, "lxml")
    rows: list[dict] = []

    for tr in soup.select("tr"):
        tds = tr.select("td")
        if len(tds) < 5:
            continue

        warnings: list[str] = []
        ekatte_code = tds[1].get_text(" ", strip=True)
        name = tds[3].get_text(" ", strip=True)
        area_text = tds[4].get_text(" ", strip=True)

        if not ekatte_code.isdigit():
            continue

        muni_region = _FORMATION_MUNI_REGION_RE.findall(area_text)
        if not muni_region:
            warnings.append("Could not parse municipality/region from settlement formations row.")
            municipality_name = None
            region_name = None
        else:
            municipality_name, region_name = muni_region[0]
            municipality_name = municipality_name.strip()
            region_name = region_name.strip()
            if len(muni_region) > 1:
                warnings.append("Multiple municipality/region pairs found; used the first pair.")

        if not name:
            warnings.append("Empty name in settlement formations row.")

        rows.append(
            {
                "source_url": f"{source_url}#ekatte-{ekatte_code}",
                "index_label": name or None,
                "name": name or None,
                "ekatte_code": ekatte_code,
                "municipality_name": municipality_name,
                "region_name": region_name,
                "type": "resort",
                "extraction_warnings": warnings,
            }
        )

    return rows
