"""Paths and static configuration for the scraper."""

from pathlib import Path

PROJECT_ROOT = Path(__file__).resolve().parent.parent

DATA_DIR = PROJECT_ROOT / "data"
OUTPUT_DIR = PROJECT_ROOT / "output"

# Legacy default export name (full place rows, future step)
DEFAULT_OUTPUT_FILENAME = "places.json"

# v1: alphabetical index → place detail URLs
BASE_URL = "https://www.ekatte.com"
INDEX_URL = f"{BASE_URL}/%D0%B0-%D1%8F"  # …/а-я
INDEX_LINKS_OUTPUT_FILENAME = "index_place_links.json"

# Proof-of-concept: detail pages from index JSON (not full crawl).
DETAIL_SAMPLE_LIMIT = 50
DETAIL_SAMPLE_OUTPUT_FILENAME = "places_detail_sample.json"
DETAIL_SAMPLE_REPORT_FILENAME = "places_detail_sample_report.json"

# Full detail crawl (all rows in ``index_place_links.json``).
DETAIL_FULL_OUTPUT_FILENAME = "places_full.json"
DETAIL_FULL_REPORT_FILENAME = "places_full_report.json"
# Settlement formations table (e.g. "Слънчев бряг") is outside the /а-я flow.
SETTLEMENT_FORMATIONS_URL = f"{BASE_URL}/%D1%81%D0%B5%D0%BB%D0%B8%D1%89%D0%BD%D0%B8-%D0%BE%D0%B1%D1%80%D0%B0%D0%B7%D1%83%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F"
# Flush ``places_full.json`` to disk after this many newly fetched rows (resume-safe).
DETAIL_FULL_SAVE_EVERY = 100
# Cap ``problematic_rows`` in the full report JSON to keep the file manageable.
FULL_DETAIL_PROBLEMATIC_ROWS_CAP = 250

PAGE_TIMEOUT_MS = 90_000

# Safety cap: per-index URL pagination (each letter + root).
MAX_INDEX_PAGES = 2500


def letter_index_url_for_page(base: str, page: int) -> str:
    """Paginate a given index base URL (root ``/а-я`` or ``/а-я/<letter>``). Page 1 has no query."""
    if page <= 1:
        return base
    sep = "&" if "?" in base else "?"
    return f"{base}{sep}page={page}"
