<?php

namespace Database\Seeders;

use App\Models\FoodPlaceEntertainmentItem;
use Illuminate\Database\Seeder;

class FoodPlaceEntertainmentItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['code' => 'karaoke', 'name' => 'Караоке', 'metadata_group' => FoodPlaceEntertainmentItem::GROUP_OFFERING],
            ['code' => 'dj_sets', 'name' => 'DJ / сетове', 'metadata_group' => FoodPlaceEntertainmentItem::GROUP_OFFERING],
            ['code' => 'bulgarian_music', 'name' => 'Българска музика', 'metadata_group' => FoodPlaceEntertainmentItem::GROUP_GENRE_BALKAN],
            ['code' => 'pop_folk', 'name' => 'Поп фолк', 'metadata_group' => FoodPlaceEntertainmentItem::GROUP_GENRE_BALKAN],
            ['code' => 'folk_traditional', 'name' => 'Народна / традиционна', 'metadata_group' => FoodPlaceEntertainmentItem::GROUP_GENRE_BALKAN],
            ['code' => 'greek_music', 'name' => 'Гръцка музика', 'metadata_group' => FoodPlaceEntertainmentItem::GROUP_GENRE_BALKAN],
            ['code' => 'serbian_music', 'name' => 'Сръбска музика', 'metadata_group' => FoodPlaceEntertainmentItem::GROUP_GENRE_BALKAN],
            ['code' => 'turkish_music', 'name' => 'Турска музика', 'metadata_group' => FoodPlaceEntertainmentItem::GROUP_GENRE_BALKAN],
            ['code' => 'jazz', 'name' => 'Джаз', 'metadata_group' => FoodPlaceEntertainmentItem::GROUP_GENRE_INTERNATIONAL],
            ['code' => 'rock', 'name' => 'Рок', 'metadata_group' => FoodPlaceEntertainmentItem::GROUP_GENRE_INTERNATIONAL],
            ['code' => 'pop', 'name' => 'Поп', 'metadata_group' => FoodPlaceEntertainmentItem::GROUP_GENRE_INTERNATIONAL],
            ['code' => 'electronic', 'name' => 'Електронна', 'metadata_group' => FoodPlaceEntertainmentItem::GROUP_GENRE_INTERNATIONAL],
            ['code' => 'latin', 'name' => 'Латино', 'metadata_group' => FoodPlaceEntertainmentItem::GROUP_GENRE_INTERNATIONAL],
            ['code' => 'classical', 'name' => 'Класическа', 'metadata_group' => FoodPlaceEntertainmentItem::GROUP_GENRE_INTERNATIONAL],
        ];

        foreach ($rows as $row) {
            FoodPlaceEntertainmentItem::query()->updateOrCreate(
                ['code' => $row['code']],
                [
                    'name' => $row['name'],
                    'metadata_group' => $row['metadata_group'],
                ]
            );
        }
    }
}
