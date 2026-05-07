<?php

namespace Database\Seeders;

use App\Models\EntityFeature;
use App\Models\EntityType;
use Illuminate\Database\Seeder;

class EntityFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accommodationTypeId = EntityType::query()
            ->where('code', 'accommodation')
            ->value('id');

        if (! $accommodationTypeId) {
            return;
        }

        $features = [
            ['code' => 'wifi', 'name' => 'Wi-Fi', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'parking', 'name' => 'Паркинг', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'ev_charging', 'name' => 'Зарядна станция за електромобили', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'air_conditioning', 'name' => 'Климатик', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'heating', 'name' => 'Отопление', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'elevator', 'name' => 'Асансьор', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'accessible_facilities', 'name' => 'Достъпна среда', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'private_bathroom', 'name' => 'Собствена баня', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'tv', 'name' => 'Телевизор', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'refrigerator', 'name' => 'Хладилник', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'kitchenette', 'name' => 'Кухненски бокс', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'workspace', 'name' => 'Работно място', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'safe', 'name' => 'Сейф', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'soundproofing', 'name' => 'Шумоизолация', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'balcony_terrace', 'name' => 'Балкон/Тераса', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'garden_courtyard', 'name' => 'Градина/двор', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'restaurant_on_site', 'name' => 'Ресторант на място', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'bar_on_site', 'name' => 'Бар на място', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'fitness_center', 'name' => 'Фитнес', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'spa_wellness', 'name' => 'СПА/уелнес зона', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'sauna', 'name' => 'Сауна', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'jacuzzi_hot_tub', 'name' => 'Джакузи/хидромасажна вана', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'indoor_pool', 'name' => 'Вътрешен басейн', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'outdoor_pool', 'name' => 'Открит басейн', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'children_play_area', 'name' => 'Детски кът/площадка', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'conference_room', 'name' => 'Конферентна зала', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'ski_storage', 'name' => 'Ски гардероб/съхранение', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'bike_storage', 'name' => 'Съхранение на велосипеди', 'feature_group' => EntityFeature::GROUP_FACILITY],
            ['code' => 'pets_allowed', 'name' => 'Домашни любимци са разрешени', 'feature_group' => EntityFeature::GROUP_POLICY],
            ['code' => 'non_smoking', 'name' => 'Непушачи', 'feature_group' => EntityFeature::GROUP_POLICY],
            ['code' => 'family_friendly', 'name' => 'Подходящо за семейства', 'feature_group' => EntityFeature::GROUP_POLICY],
            ['code' => 'adults_only', 'name' => 'Само за възрастни', 'feature_group' => EntityFeature::GROUP_POLICY],
        ];

        foreach ($features as $feature) {
            EntityFeature::query()->updateOrCreate(
                ['code' => $feature['code']],
                [
                    'entity_type_id' => $accommodationTypeId,
                    'name' => $feature['name'],
                    'feature_group' => $feature['feature_group'],
                ]
            );
        }
    }
}
