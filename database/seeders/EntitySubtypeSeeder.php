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
            ],
            'attraction' => [
                ['code' => 'museum', 'name' => 'Музей'],
                ['code' => 'fortress', 'name' => 'Крепост'],
                ['code' => 'beach', 'name' => 'Плаж'],
                ['code' => 'waterfall', 'name' => 'Водопад'],
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

