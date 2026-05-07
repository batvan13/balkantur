<?php

namespace Database\Seeders;

use App\Models\Entity;
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
                ['code' => 'gallery', 'name' => 'Галерия'],
                ['code' => 'monument', 'name' => 'Паметник'],
                ['code' => 'archaeological_site', 'name' => 'Археологически обект'],
                ['code' => 'fortress', 'name' => 'Крепост'],
                ['code' => 'castle', 'name' => 'Замък'],
                ['code' => 'palace', 'name' => 'Дворец'],
                ['code' => 'monastery', 'name' => 'Манастир'],
                ['code' => 'church_temple', 'name' => 'Църква / Храм'],
                ['code' => 'chapel', 'name' => 'Параклис'],
                ['code' => 'ethnographic_complex', 'name' => 'Етнографски комплекс'],
                ['code' => 'observatory', 'name' => 'Обсерватория'],
                ['code' => 'planetarium', 'name' => 'Планетариум'],
                ['code' => 'park', 'name' => 'Парк'],
                ['code' => 'protected_area', 'name' => 'Защитена местност'],
                ['code' => 'reserve', 'name' => 'Резерват'],
                ['code' => 'national_park', 'name' => 'Национален парк'],
                ['code' => 'nature_park', 'name' => 'Природен парк'],
                ['code' => 'eco_trail', 'name' => 'Екопътека'],
                ['code' => 'hiking_trail', 'name' => 'Туристическа пътека'],
                ['code' => 'cycling_route', 'name' => 'Веломаршрут'],
                ['code' => 'cave', 'name' => 'Пещера'],
                ['code' => 'waterfall', 'name' => 'Водопад'],
                ['code' => 'peak', 'name' => 'Връх'],
                ['code' => 'lake', 'name' => 'Езеро'],
                ['code' => 'river', 'name' => 'Река'],
                ['code' => 'beach', 'name' => 'Плаж'],
                ['code' => 'zoo', 'name' => 'Зоопарк'],
                ['code' => 'aquapark', 'name' => 'Аквапарк'],
                ['code' => 'outdoor_pool', 'name' => 'Открит басейн'],
                ['code' => 'amusement_park', 'name' => 'Увеселителен парк'],
                ['code' => 'golf_course', 'name' => 'Голф игрище'],
                ['code' => 'paintball_field', 'name' => 'Пейнтбол игрище'],
                ['code' => 'karting_track', 'name' => 'Картинг писта'],
                ['code' => 'horse_riding_base', 'name' => 'Конна база'],
                ['code' => 'boat_tour', 'name' => 'Лодъчна разходка / тур'],
                ['code' => 'cable_car_station', 'name' => 'Лифт / въжена линия (станция)'],
                ['code' => 'shopping_center', 'name' => 'Търговски център'],
                ['code' => 'airport', 'name' => 'Летище'],
                ['code' => 'landmark', 'name' => 'Забележителност'],
                ['code' => 'island', 'name' => 'Остров'],
                ['code' => 'cape', 'name' => 'Нос'],
                ['code' => 'rock_formation', 'name' => 'Скална формация'],
                ['code' => 'spring', 'name' => 'Извор'],
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

        $attractionTypeId = EntityType::query()
            ->where('code', 'attraction')
            ->value('id');

        if (! $attractionTypeId) {
            return;
        }

        $approvedAttractionCodes = collect($subtypesByTypeCode['attraction'])
            ->pluck('code')
            ->all();

        $churchSubtypeId = EntitySubtype::query()
            ->where('entity_type_id', $attractionTypeId)
            ->where('code', 'church')
            ->value('id');

        $churchTempleSubtypeId = EntitySubtype::query()
            ->where('entity_type_id', $attractionTypeId)
            ->where('code', 'church_temple')
            ->value('id');

        if ($churchSubtypeId && $churchTempleSubtypeId && $churchSubtypeId !== $churchTempleSubtypeId) {
            Entity::query()
                ->where('entity_subtype_id', $churchSubtypeId)
                ->update(['entity_subtype_id' => $churchTempleSubtypeId]);
        }

        $legacyAttractionSubtypes = EntitySubtype::query()
            ->where('entity_type_id', $attractionTypeId)
            ->whereNotIn('code', $approvedAttractionCodes)
            ->get(['id']);

        foreach ($legacyAttractionSubtypes as $legacySubtype) {
            $isInUse = Entity::query()
                ->where('entity_subtype_id', $legacySubtype->id)
                ->exists();

            if (! $isInUse) {
                $legacySubtype->delete();
            }
        }
    }
}

