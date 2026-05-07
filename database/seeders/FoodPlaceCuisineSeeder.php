<?php

namespace Database\Seeders;

use App\Models\FoodPlaceCuisine;
use Illuminate\Database\Seeder;

class FoodPlaceCuisineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cuisines = [
            ['code' => 'bulgarian', 'name' => 'Българска'],
            ['code' => 'balkan', 'name' => 'Балканска'],
            ['code' => 'greek', 'name' => 'Гръцка'],
            ['code' => 'turkish', 'name' => 'Турска'],
            ['code' => 'serbian', 'name' => 'Сръбска'],
            ['code' => 'armenian', 'name' => 'Арменска'],
            ['code' => 'italian', 'name' => 'Италианска'],
            ['code' => 'french', 'name' => 'Френска'],
            ['code' => 'spanish', 'name' => 'Испанска'],
            ['code' => 'russian', 'name' => 'Руска'],
            ['code' => 'mediterranean', 'name' => 'Средиземноморска'],
            ['code' => 'european', 'name' => 'Европейска'],
            ['code' => 'arabic_middle_eastern', 'name' => 'Арабска / Близкоизточна'],
            ['code' => 'chinese', 'name' => 'Китайска'],
            ['code' => 'thai', 'name' => 'Тайландска'],
            ['code' => 'indian', 'name' => 'Индийска'],
            ['code' => 'japanese', 'name' => 'Японска'],
            ['code' => 'asian', 'name' => 'Азиатска'],
            ['code' => 'latin_american', 'name' => 'Латиноамериканска'],
            ['code' => 'vegetarian', 'name' => 'Вегетарианска'],
            ['code' => 'vegan', 'name' => 'Веган'],
            ['code' => 'international', 'name' => 'Интернационална'],
        ];

        foreach ($cuisines as $cuisine) {
            FoodPlaceCuisine::query()->updateOrCreate(
                ['code' => $cuisine['code']],
                ['name' => $cuisine['name']]
            );
        }
    }
}
