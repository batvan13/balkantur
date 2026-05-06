"""
Full detail crawl: every URL in ``output/index_place_links.json`` →
``output/places_full.json`` plus ``output/places_full_report.json``.

Progress is written to ``places_full.json`` every ``DETAIL_FULL_SAVE_EVERY`` new rows.
If ``places_full.json`` already exists, existing ``source_url`` values are skipped (resume).

Run: ``python full_details.py`` from ``tools/bg_places_scraper``.
"""

import json
from pathlib import Path

from playwright.sync_api import sync_playwright

from src import config
from src.detail_report import build_quality_report
from src.exporter import export_json
from src.fetcher import goto_url
from src.parser import parse_place_detail, parse_settlement_formations


def _load_all_index_links() -> list[dict]:
    path = Path(config.OUTPUT_DIR) / config.INDEX_LINKS_OUTPUT_FILENAME
    if not path.is_file():
        raise FileNotFoundError(f"Missing index file: {path}")
    return json.loads(path.read_text(encoding="utf-8"))


def _places_full_path() -> Path:
    return Path(config.OUTPUT_DIR) / config.DETAIL_FULL_OUTPUT_FILENAME


def _load_existing_records(path: Path) -> list[dict]:
    """Load prior crawl results; dedupe by ``source_url`` (first occurrence wins)."""
    if not path.is_file():
        return []
    raw = path.read_text(encoding="utf-8").strip()
    if not raw:
        return []
    try:
        data = json.loads(raw)
    except json.JSONDecodeError:
        print(f"Warning: could not parse {path.name}; starting with an empty dataset.")
        return []
    if not isinstance(data, list):
        return []
    seen: set[str] = set()
    out: list[dict] = []
    for row in data:
        u = row.get("source_url")
        if not u or u in seen:
            continue
        seen.add(u)
        out.append(row)
    return out


def _write_json_atomic(path: Path, records: list[dict]) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    tmp = path.parent / (path.name + ".tmp")
    tmp.write_text(json.dumps(records, ensure_ascii=False, indent=2), encoding="utf-8")
    tmp.replace(path)


def _merge_settlement_formations(records: list[dict], formations: list[dict]) -> tuple[list[dict], int]:
    """Append only formation rows whose ``ekatte_code`` is not already present."""
    existing_codes = {
        str(r.get("ekatte_code")).strip()
        for r in records
        if r.get("ekatte_code") is not None and str(r.get("ekatte_code")).strip()
    }

    added = 0
    for row in formations:
        code = str(row.get("ekatte_code") or "").strip()
        if not code or code in existing_codes:
            continue
        records.append(row)
        existing_codes.add(code)
        added += 1

    return records, added


def main() -> None:
    entries = _load_all_index_links()
    out_path = _places_full_path()
    records = _load_existing_records(out_path)
    done_urls = {r["source_url"] for r in records if r.get("source_url")}
    pending = sum(1 for row in entries if row["url"] not in done_urls)

    print(f"Index URLs: {len(entries)} | Already in {out_path.name}: {len(done_urls)} | Pending fetch: {pending}")

    since_flush = 0
    fetched = 0

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        try:
            page = browser.new_page()
            for i, row in enumerate(entries, start=1):
                url = row["url"]
                if url in done_urls:
                    continue
                label = row.get("label")
                goto_url(page, url)
                try:
                    page.locator("button.agree-button.eu-cookie-compliance-default-button").click(
                        timeout=2500
                    )
                except Exception:
                    pass
                rec = parse_place_detail(page.content(), url, index_label=label)
                records.append(rec)
                done_urls.add(url)
                fetched += 1
                since_flush += 1

                if since_flush >= config.DETAIL_FULL_SAVE_EVERY:
                    _write_json_atomic(out_path, records)
                    since_flush = 0
                    print(f"Checkpoint saved: {len(records)} rows (fetched this run: {fetched})")

                if fetched > 0 and fetched % 100 == 0:
                    print(f"Fetched {fetched} new / index position {i}/{len(entries)}")
        finally:
            browser.close()

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        try:
            page = browser.new_page()
            goto_url(page, config.SETTLEMENT_FORMATIONS_URL)
            formation_rows = parse_settlement_formations(page.content(), config.SETTLEMENT_FORMATIONS_URL)
        finally:
            browser.close()

    records, formations_added = _merge_settlement_formations(records, formation_rows)

    _write_json_atomic(out_path, records)
    print(f"Final dataset written: {len(records)} rows to {out_path}")
    print(
        f"Settlement formations parsed: {len(formation_rows)} | Added by new ekatte_code: {formations_added}"
    )

    report = build_quality_report(
        records,
        total_key="total_processed_rows",
        problematic_row_limit=config.FULL_DETAIL_PROBLEMATIC_ROWS_CAP,
    )
    out_report = export_json(report, filename=config.DETAIL_FULL_REPORT_FILENAME)

    print(f"Total detail records: {len(records)}")
    print(f"Report JSON: {out_report}")
    print(
        f"Report: warnings_rows={report['rows_with_any_extraction_warnings']}, "
        f"null_type={report['rows_with_null_type']}, "
        f"missing_ekatte={report['rows_with_missing_ekatte_code']}"
    )


if __name__ == "__main__":
    main()
