<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Seed Balkan region countries (controlled reference data).
     */
    public function run(): void
    {
        $rows = [
            ['code' => 'BG', 'name' => 'България'],
            ['code' => 'GR', 'name' => 'Гърция'],
            ['code' => 'RO', 'name' => 'Румъния'],
            ['code' => 'RS', 'name' => 'Сърбия'],
            ['code' => 'MK', 'name' => 'Северна Македония'],
            ['code' => 'AL', 'name' => 'Албания'],
            ['code' => 'ME', 'name' => 'Черна гора'],
            ['code' => 'BA', 'name' => 'Босна и Херцеговина'],
            ['code' => 'HR', 'name' => 'Хърватия'],
            ['code' => 'SI', 'name' => 'Словения'],
            ['code' => 'XK', 'name' => 'Косово'],
        ];

        foreach ($rows as $row) {
            Country::query()->updateOrCreate(
                ['code' => $row['code']],
                ['name' => $row['name']]
            );
        }
    }
}
