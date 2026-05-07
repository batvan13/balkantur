<?php

namespace Database\Seeders;

use App\Models\EntitySubtype;
use App\Models\EntityType;
use Illuminate\Database\Seeder;

class EntitySubtypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subtypesByTypeCode = [
            'accommodation' => [
                ['code' => 'hotel', 'name' => 'Хотел'],
                ['code' => 'guest_house', 'name' => 'Къща за гости'],
                ['code' => 'apartment', 'name' => 'Апартамент'],
                ['code' => 'villa', 'name' => 'Вила'],
            ],
            'food_place' => [
                ['code' => 'restaurant', 'name' => 'Ресторант'],
                ['code' => 'tavern', 'name' => 'Механа'],
                ['code' => 'bistro', 'name' => 'Бистро'],
                ['code' => 'bar', 'name' => 'Бар'],
                ['code' => 'cafe', 'name' => 'Кафене'],
                ['code' => 'pizzeria', 'name' => 'Пицария'],
                ['code' => 'fast_food', 'name' => 'Бързо хранене'],
                ['code' => 'pastry_shop', 'name' => 'Сладкарница'],
                ['code' => 'bakery', 'name' => 'Пекарна'],
                ['code' => 'fish_restaurant', 'name' => 'Рибен ресторант'],
                ['code' => 'steakhouse', 'name' => 'Стекхаус'],
                ['code' => 'wine_bar', 'name' => 'Винен бар'],
            ],
            'attraction' => [
                ['code' => 'museum', 'name' => 'Музей'],
                ['code' => 'fortress', 'name' => 'Крепост'],
                ['code' => 'beach', 'name' => 'Плаж'],
                ['code' => 'waterfall', 'name' => 'Водопад'],
                ['code' => 'monastery', 'name' => 'Манастир'],
                ['code' => 'church', 'name' => 'Църква'],
                ['code' => 'cave', 'name' => 'Пещера'],
                ['code' => 'peak', 'name' => 'Връх'],
                ['code' => 'lake', 'name' => 'Езеро'],
                ['code' => 'river', 'name' => 'Река'],
                ['code' => 'park', 'name' => 'Парк'],
                ['code' => 'nature_reserve', 'name' => 'Природен резерват'],
                ['code' => 'eco_trail', 'name' => 'Екопътека'],
                ['code' => 'landmark', 'name' => 'Забележителност'],
                ['code' => 'archaeological_site', 'name' => 'Археологически обект'],
                ['code' => 'ethnographic_complex', 'name' => 'Етнографски комплекс'],
                ['code' => 'amusement_park', 'name' => 'Увеселителен парк'],
                ['code' => 'zoo', 'name' => 'Зоопарк'],
                ['code' => 'aquapark', 'name' => 'Аквапарк'],
            ],
        ];

        foreach ($subtypesByTypeCode as $entityTypeCode => $subtypes) {
            $entityTypeId = EntityType::query()
                ->where('code', $entityTypeCode)
                ->value('id');

            if (! $entityTypeId) {
                continue;
            }

            foreach ($subtypes as $subtype) {
                EntitySubtype::query()->updateOrCreate(
                    ['code' => $subtype['code']],
                    [
                        'entity_type_id' => $entityTypeId,
                        'name' => $subtype['name'],
                    ]
                );
            }
        }
    }
}

