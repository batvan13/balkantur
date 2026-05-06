<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\Place;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportBgPlacesCommand extends Command
{
    protected $signature = 'places:import-bg {--file=tools/bg_places_scraper/output/places_full.json : Path to places JSON file}';

    protected $description = 'Import Bulgarian places from scraper JSON using ekatte_code as stable key';

    public function handle(): int
    {
        $country = Country::query()
            ->whereIn('code', ['BG', 'bg'])
            ->first();

        if (! $country) {
            $this->error('Country with code BG was not found in countries table.');

            return self::FAILURE;
        }

        $filePath = base_path((string) $this->option('file'));
        if (! File::exists($filePath)) {
            $this->error("Import file not found: {$filePath}");

            return self::FAILURE;
        }

        try {
            $rows = json_decode(File::get($filePath), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            $this->error('Invalid JSON in import file: '.$exception->getMessage());

            return self::FAILURE;
        }

        if (! is_array($rows)) {
            $this->error('Import JSON must contain a top-level array of place rows.');

            return self::FAILURE;
        }

        $totalRowsRead = count($rows);
        $createdCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $skippedReasons = [
            'missing_ekatte_code' => 0,
            'missing_name' => 0,
            'null_or_empty_type' => 0,
        ];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                $skippedCount++;
                $skippedReasons['missing_ekatte_code']++;
                continue;
            }

            $ekatteCode = trim((string) ($row['ekatte_code'] ?? ''));
            $name = trim((string) ($row['name'] ?? ''));
            $type = $row['type'] ?? null;
            $typeValue = is_string($type) ? trim($type) : null;

            if ($ekatteCode === '') {
                $skippedCount++;
                $skippedReasons['missing_ekatte_code']++;
                continue;
            }

            if ($name === '') {
                $skippedCount++;
                $skippedReasons['missing_name']++;
                continue;
            }

            if ($typeValue === null || $typeValue === '') {
                $skippedCount++;
                $skippedReasons['null_or_empty_type']++;
                continue;
            }

            $exists = Place::query()
                ->where('ekatte_code', $ekatteCode)
                ->exists();

            Place::query()->updateOrCreate(
                ['ekatte_code' => $ekatteCode],
                [
                    'country_id' => $country->id,
                    'name' => $name,
                    'type' => $typeValue,
                    'municipality_name' => $this->normalizeNullableString($row['municipality_name'] ?? null),
                    'region_name' => $this->normalizeNullableString($row['region_name'] ?? null),
                ]
            );

            if ($exists) {
                $updatedCount++;
            } else {
                $createdCount++;
            }
        }

        $this->newLine();
        $this->info('Places import completed.');
        $this->line("Total rows read: {$totalRowsRead}");
        $this->line("Imported/created count: {$createdCount}");
        $this->line("Updated count: {$updatedCount}");
        $this->line("Skipped count: {$skippedCount}");
        $this->line('Skipped reasons:');
        foreach ($skippedReasons as $reason => $count) {
            $this->line("- {$reason}: {$count}");
        }

        return self::SUCCESS;
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}

