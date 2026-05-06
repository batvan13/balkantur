"""Write normalized records as UTF-8 JSON for Laravel import."""

import json

from src import config


def export_json(records: list[dict], filename: str | None = None) -> str:
    """
    Serialize records to JSON under output/. Returns the written file path as str.
    """
    config.OUTPUT_DIR.mkdir(parents=True, exist_ok=True)
    name = filename or config.DEFAULT_OUTPUT_FILENAME
    path = config.OUTPUT_DIR / name
    path.write_text(
        json.dumps(records, ensure_ascii=False, indent=2),
        encoding="utf-8",
    )
    return str(path)
