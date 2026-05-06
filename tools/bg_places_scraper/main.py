"""Orchestrate full alphabetical index crawl → parse → normalize → export."""

from playwright.sync_api import sync_playwright

from src import config
from src.exporter import export_json
from src.fetcher import collect_index_bases, goto_index_page
from src.normalizer import normalize
from src.parser import parse


def main() -> None:
    all_rows: list[dict] = []

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        try:
            page = browser.new_page()
            bases = collect_index_bases(page)
            for base in bases:
                prev_unique = len(normalize(all_rows))
                for page_num in range(1, config.MAX_INDEX_PAGES + 1):
                    goto_index_page(page, base, page_num)
                    all_rows.extend(parse(page.content()))
                    now_unique = len(normalize(all_rows))
                    if now_unique == prev_unique:
                        break
                    prev_unique = now_unique
        finally:
            browser.close()

    records = normalize(all_rows)
    out_path = export_json(records, filename=config.INDEX_LINKS_OUTPUT_FILENAME)
    print(f"Total unique place links: {len(records)}")
    print(f"Wrote to {out_path}")


if __name__ == "__main__":
    main()
