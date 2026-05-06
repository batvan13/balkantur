"""Load index pages using a headless Chromium (Playwright)."""

from playwright.sync_api import Page, sync_playwright

from src import config
from src.parser import parse_letter_subindex_urls


def fetch(url: str | None = None) -> str:
    """Open a single URL in a new browser session and return the document HTML."""
    target = url or config.INDEX_URL
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        try:
            page = browser.new_page()
            page.goto(target, wait_until="networkidle", timeout=config.PAGE_TIMEOUT_MS)
            return page.content()
        finally:
            browser.close()


def collect_index_bases(page: Page) -> list[str]:
    """
    Open the root ``/а-я`` index and return that URL plus every ``/а-я/<letter>`` sub-index.
    """
    page.goto(config.INDEX_URL, wait_until="networkidle", timeout=config.PAGE_TIMEOUT_MS)
    letters = parse_letter_subindex_urls(page.content())
    return sorted({config.INDEX_URL, *letters})


def goto_index_page(page: Page, base: str, page_num: int) -> None:
    """Navigate to one paginated index page (``page_num`` 1 = no ``?page=``)."""
    url = config.letter_index_url_for_page(base, page_num)
    page.goto(url, wait_until="networkidle", timeout=config.PAGE_TIMEOUT_MS)


def goto_url(page: Page, url: str) -> None:
    """Navigate to an arbitrary page (e.g. place detail)."""
    page.goto(url, wait_until="networkidle", timeout=config.PAGE_TIMEOUT_MS)
