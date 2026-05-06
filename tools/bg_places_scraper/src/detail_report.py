"""Build machine-readable quality reports for detail extraction runs."""

from collections import Counter


def missing(value: object) -> bool:
    return value is None or (isinstance(value, str) and not value.strip())


def build_quality_report(
    records: list[dict],
    *,
    total_key: str = "total_processed_rows",
    problematic_row_limit: int | None = None,
) -> dict:
    """
    Summarise extraction health. ``warning_type_counts`` counts individual warning strings
    across all rows (a row with two warnings contributes to two keys).
    """
    n = len(records)
    with_warnings = 0
    null_type = 0
    miss_ekatte = 0
    miss_muni = 0
    miss_region = 0
    warning_type_counts: Counter[str] = Counter()
    problematic_all: list[dict] = []

    for r in records:
        warnings = list(r.get("extraction_warnings") or [])
        if warnings:
            with_warnings += 1
            for w in warnings:
                warning_type_counts[w] += 1
        if r.get("type") is None:
            null_type += 1
        if missing(r.get("ekatte_code")):
            miss_ekatte += 1
        if missing(r.get("municipality_name")):
            miss_muni += 1
        if missing(r.get("region_name")):
            miss_region += 1

        null_critical: list[str] = []
        if missing(r.get("name")):
            null_critical.append("name")
        if missing(r.get("ekatte_code")):
            null_critical.append("ekatte_code")
        if missing(r.get("municipality_name")):
            null_critical.append("municipality_name")
        if missing(r.get("region_name")):
            null_critical.append("region_name")
        if r.get("type") is None:
            null_critical.append("type")

        if warnings or null_critical:
            problematic_all.append(
                {
                    "source_url": r.get("source_url"),
                    "index_label": r.get("index_label"),
                    "extraction_warnings": warnings,
                    "null_critical_fields": null_critical,
                }
            )

    out: dict = {
        total_key: n,
        "rows_with_any_extraction_warnings": with_warnings,
        "rows_with_null_type": null_type,
        "rows_with_missing_ekatte_code": miss_ekatte,
        "rows_with_missing_municipality_name": miss_muni,
        "rows_with_missing_region_name": miss_region,
        "warning_type_counts": dict(warning_type_counts.most_common()),
        "problematic_rows": problematic_all,
    }

    if problematic_row_limit is not None and len(problematic_all) > problematic_row_limit:
        omitted = len(problematic_all) - problematic_row_limit
        out["problematic_rows"] = problematic_all[:problematic_row_limit]
        out["problematic_rows_truncated"] = True
        out["problematic_rows_omitted"] = omitted
    elif problematic_row_limit is not None:
        out["problematic_rows_truncated"] = False
        out["problematic_rows_omitted"] = 0

    return out
