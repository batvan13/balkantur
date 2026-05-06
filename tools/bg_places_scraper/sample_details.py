"""
Load a limited slice of ``index_place_links.json``, fetch detail pages with
Playwright, write ``places_detail_sample.json`` and a machine-readable quality
report ``places_detail_sample_report.json``.

Run: ``python sample_details.py`` from ``tools/bg_places_scraper``.
"""

import json
from pathlib import Path

from playwright.sync_api import sync_playwright

from src import config
from src.detail_report import build_quality_report
from src.exporter import export_json
from src.fetcher import goto_url
from src.parser import parse_place_detail


def _load_sample_links(limit: int) -> list[dict]:
    path = Path(config.OUTPUT_DIR) / config.INDEX_LINKS_OUTPUT_FILENAME
    if not path.is_file():
        raise FileNotFoundError(f"Missing index file: {path}")
    rows = json.loads(path.read_text(encoding="utf-8"))
    return rows[:limit]


def main() -> None:
    entries = _load_sample_links(config.DETAIL_SAMPLE_LIMIT)
    records: list[dict] = []

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        try:
            page = browser.new_page()
            for row in entries:
                url = row["url"]
                label = row.get("label")
                goto_url(page, url)
                try:
                    page.locator("button.agree-button.eu-cookie-compliance-default-button").click(
                        timeout=2500
                    )
                except Exception:
                    pass
                records.append(parse_place_detail(page.content(), url, index_label=label))
        finally:
            browser.close()

    out_detail = export_json(records, filename=config.DETAIL_SAMPLE_OUTPUT_FILENAME)
    report = build_quality_report(
        records,
        total_key="total_sampled_rows",
        problematic_row_limit=None,
    )
    out_report = export_json(report, filename=config.DETAIL_SAMPLE_REPORT_FILENAME)

    print(f"Total detail records written: {len(records)}")
    print(f"Detail JSON: {out_detail}")
    print(f"Report JSON: {out_report}")
    print(
        f"Report summary: warnings={report['rows_with_any_extraction_warnings']}, "
        f"null_type={report['rows_with_null_type']}, "
        f"missing_ekatte={report['rows_with_missing_ekatte_code']}, "
        f"problematic_rows_listed={len(report['problematic_rows'])}"
    )


if __name__ == "__main__":
    main()
