<?php

namespace Database\Seeders;

use App\Models\EntityType;
use Illuminate\Database\Seeder;

class EntityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entityTypes = [
            [
                'code' => 'accommodation',
                'name' => 'Място за настаняване',
            ],
            [
                'code' => 'food_place',
                'name' => 'Място за хранене',
            ],
            [
                'code' => 'attraction',
                'name' => 'Атракция',
            ],
        ];

        foreach ($entityTypes as $entityType) {
            EntityType::query()->updateOrCreate(
                ['code' => $entityType['code']],
                ['name' => $entityType['name']]
            );
        }
    }
}

