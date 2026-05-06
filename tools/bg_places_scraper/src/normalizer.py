"""Dedupe and sort index link rows before export."""


def normalize(records: list[dict]) -> list[dict]:
    """Drop duplicate URLs; preserve first seen label; sort by URL."""
    by_url: dict[str, dict] = {}
    for row in records:
        url = row.get("url")
        if not url or url in by_url:
            continue
        by_url[url] = {"url": url, "label": row.get("label")}
    return sorted(by_url.values(), key=lambda r: r["url"])
