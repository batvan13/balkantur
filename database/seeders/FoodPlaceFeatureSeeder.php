<?php

namespace Database\Seeders;

use App\Models\FoodPlaceFeature;
use Illuminate\Database\Seeder;

class FoodPlaceFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['code' => 'wifi', 'name' => 'Wi-Fi', 'feature_group' => FoodPlaceFeature::GROUP_FACILITY],
            ['code' => 'outdoor_seating', 'name' => 'Място на открито', 'feature_group' => FoodPlaceFeature::GROUP_FACILITY],
            ['code' => 'terrace', 'name' => 'Тераса', 'feature_group' => FoodPlaceFeature::GROUP_FACILITY],
            ['code' => 'garden', 'name' => 'Градина / дворна зона', 'feature_group' => FoodPlaceFeature::GROUP_FACILITY],
            ['code' => 'parking', 'name' => 'Паркинг', 'feature_group' => FoodPlaceFeature::GROUP_FACILITY],
            ['code' => 'kids_area', 'name' => 'Детски кът', 'feature_group' => FoodPlaceFeature::GROUP_FACILITY],
            ['code' => 'accessible_facilities', 'name' => 'Достъпна среда', 'feature_group' => FoodPlaceFeature::GROUP_FACILITY],
            ['code' => 'delivery', 'name' => 'Доставка', 'feature_group' => FoodPlaceFeature::GROUP_SERVICE],
            ['code' => 'takeaway', 'name' => 'За вкъщи', 'feature_group' => FoodPlaceFeature::GROUP_SERVICE],
            ['code' => 'catering', 'name' => 'Кетъринг', 'feature_group' => FoodPlaceFeature::GROUP_SERVICE],
            ['code' => 'reservations', 'name' => 'Резервации', 'feature_group' => FoodPlaceFeature::GROUP_SERVICE],
            ['code' => 'lunch_menu', 'name' => 'Обедно меню', 'feature_group' => FoodPlaceFeature::GROUP_SERVICE],
            ['code' => 'animators', 'name' => 'Аниматори', 'feature_group' => FoodPlaceFeature::GROUP_ENTERTAINMENT],
            ['code' => 'live_music', 'name' => 'Жива музика', 'feature_group' => FoodPlaceFeature::GROUP_ENTERTAINMENT],
            ['code' => 'card_payment', 'name' => 'Плащане с карта', 'feature_group' => FoodPlaceFeature::GROUP_PAYMENT_ACCESS],
        ];

        foreach ($rows as $row) {
            FoodPlaceFeature::query()->updateOrCreate(
                ['code' => $row['code']],
                [
                    'name' => $row['name'],
                    'feature_group' => $row['feature_group'],
                ]
            );
        }
    }
}
