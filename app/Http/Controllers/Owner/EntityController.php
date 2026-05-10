<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Entity;
use App\Models\EntityFeature;
use App\Models\EntitySubtype;
use App\Models\EntityType;
use App\Models\FoodPlaceCuisine;
use App\Models\FoodPlaceEntertainmentItem;
use App\Models\FoodPlaceFeature;
use App\Models\Place;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EntityController extends Controller
{
    private const CLASSIFIABLE_TYPE_CODES = [
        'accommodation',
        'food_place',
    ];

    private const ACCOMMODATION_CLASSIFICATIONS = [
        '1 звезда',
        '2 звезди',
        '3 звезди',
        '4 звезди',
        '5 звезди',
    ];

    private const FEATURED_TYPE_CODE = 'accommodation';

    private const CUISINE_TYPE_CODE = 'food_place';

    private const FOOD_PLACE_FEATURES_TYPE_CODE = 'food_place';

    private const FOOD_PLACE_ENTERTAINMENT_TYPE_CODE = 'food_place';

    public function index(Request $request): View
    {
        $entities = Entity::query()
            ->where('user_id', $request->user()->id)
            ->with(['entityType', 'entitySubtype', 'place'])
            ->latest()
            ->get();

        return view('owner.dashboard', [
            'entities' => $entities,
        ]);
    }

    public function create(Request $request): View
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'exists:entity_types,code'],
        ]);

        $entityType = EntityType::query()
            ->where('code', $validated['type'])
            ->firstOrFail();

        $selectedPlace = null;
        $oldPlaceId = old('place_id');
        if ($oldPlaceId) {
            $selectedPlace = Place::query()
                ->whereKey($oldPlaceId)
                ->first(['id', 'name', 'type', 'municipality_name', 'region_name']);
        }

        return view('owner.entities.create', [
            'entityType' => $entityType,
            'entitySubtypes' => EntitySubtype::query()
                ->where('entity_type_id', $entityType->id)
                ->orderBy('name')
                ->get(['id', 'name']),
            'entityFeatures' => $this->loadTypeFeatures($entityType),
            'foodPlaceCuisines' => $this->loadTypeCuisines($entityType),
            'foodPlaceFeatureOptions' => $this->loadFoodPlaceFeatureOptions($entityType),
            'foodPlaceEntertainmentOptions' => $this->loadFoodPlaceEntertainmentOptions($entityType),
            'countries' => Country::query()->orderBy('name')->get(['id', 'name']),
            'selectedPlace' => $selectedPlace,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'exists:entity_types,code'],
            'entity_subtype_id' => ['nullable', 'integer', 'exists:entity_subtypes,id'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'place_id' => ['required', 'integer', 'exists:places,id'],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'classification' => ['nullable', 'string', 'max:255'],
            'feature_ids' => ['nullable', 'array'],
            'feature_ids.*' => ['integer'],
            'cuisine_ids' => ['nullable', 'array'],
            'cuisine_ids.*' => ['integer'],
            'food_place_feature_ids' => ['nullable', 'array'],
            'food_place_feature_ids.*' => ['integer'],
            'food_place_entertainment_item_ids' => ['nullable', 'array'],
            'food_place_entertainment_item_ids.*' => ['integer'],
        ]);

        $entityType = $this->resolveEntityType($validated);
        $entitySubtypeId = $this->resolveEntitySubtypeId($validated, $entityType->id);
        $classification = $this->resolveClassification($request, $entityType->code);
        $featureIds = $this->resolveFeatureIds($validated, $entityType);
        $cuisineIds = $this->resolveCuisineIds($validated, $entityType);
        $foodPlaceFeatureIds = $this->resolveFoodPlaceFeatureIds($validated, $entityType);
        $foodPlaceEntertainmentItemIds = $this->resolveFoodPlaceEntertainmentItemIds($validated, $entityType);

        $entity = Entity::query()->create([
            'user_id' => $request->user()->id,
            'entity_type_id' => $entityType->id,
            'entity_subtype_id' => $entitySubtypeId,
            'country_id' => $validated['country_id'],
            'place_id' => $validated['place_id'],
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'website' => $validated['website'] ?? null,
            'classification' => $classification,
        ]);
        $entity->features()->sync($featureIds);
        $entity->cuisines()->sync($cuisineIds);
        $entity->foodPlaceFeatures()->sync($foodPlaceFeatureIds);
        $entity->foodPlaceEntertainmentItems()->sync($foodPlaceEntertainmentItemIds);

        $entity->syncBulgarianTranslation(
            $validated['name'],
            $validated['address'] ?? null,
            $validated['description'] ?? null
        );

        return redirect()
            ->route('owner.dashboard')
            ->with('success', 'Обектът е добавен успешно.');
    }

    public function show(Request $request, Entity $entity): View
    {
        $this->authorizeOwnerEntityAccess($request, $entity);

        $entity->loadMissing([
            'entityType',
            'entitySubtype',
            'country',
            'place',
            'bgTranslation',
            'features',
            'cuisines',
            'foodPlaceFeatures',
            'foodPlaceEntertainmentItems',
        ]);

        return view('owner.entities.show', [
            'entity' => $entity,
        ]);
    }

    public function edit(Request $request, Entity $entity): View
    {
        $this->authorizeOwnerEntityAccess($request, $entity);
        $entity->loadMissing(['entityType', 'place', 'bgTranslation', 'features', 'cuisines', 'foodPlaceFeatures', 'foodPlaceEntertainmentItems']);

        $selectedPlace = null;
        $oldPlaceId = old('place_id');
        if ($oldPlaceId) {
            $selectedPlace = Place::query()
                ->whereKey($oldPlaceId)
                ->first(['id', 'name', 'type', 'municipality_name', 'region_name']);
        } else {
            $selectedPlace = $entity->place
                ? Place::query()->whereKey($entity->place_id)->first(['id', 'name', 'type', 'municipality_name', 'region_name'])
                : null;
        }

        return view('owner.entities.edit', [
            'entity' => $entity,
            'entityType' => $entity->entityType,
            'entitySubtypes' => EntitySubtype::query()
                ->where('entity_type_id', $entity->entity_type_id)
                ->orderBy('name')
                ->get(['id', 'name']),
            'entityFeatures' => $this->loadTypeFeatures($entity->entityType),
            'foodPlaceCuisines' => $this->loadTypeCuisines($entity->entityType),
            'foodPlaceFeatureOptions' => $this->loadFoodPlaceFeatureOptions($entity->entityType),
            'foodPlaceEntertainmentOptions' => $this->loadFoodPlaceEntertainmentOptions($entity->entityType),
            'countries' => Country::query()->orderBy('name')->get(['id', 'name']),
            'selectedPlace' => $selectedPlace,
        ]);
    }

    public function update(Request $request, Entity $entity): RedirectResponse
    {
        $this->authorizeOwnerEntityAccess($request, $entity);

        $validated = $request->validate([
            'entity_subtype_id' => ['nullable', 'integer', 'exists:entity_subtypes,id'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'place_id' => ['required', 'integer', 'exists:places,id'],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'classification' => ['nullable', 'string', 'max:255'],
            'feature_ids' => ['nullable', 'array'],
            'feature_ids.*' => ['integer'],
            'cuisine_ids' => ['nullable', 'array'],
            'cuisine_ids.*' => ['integer'],
            'food_place_feature_ids' => ['nullable', 'array'],
            'food_place_feature_ids.*' => ['integer'],
            'food_place_entertainment_item_ids' => ['nullable', 'array'],
            'food_place_entertainment_item_ids.*' => ['integer'],
        ]);

        $entitySubtypeId = $this->resolveEntitySubtypeId($validated, $entity->entity_type_id);
        $classification = $this->resolveClassification($request, $entity->entityType->code);
        $featureIds = $this->resolveFeatureIds($validated, $entity->entityType);
        $cuisineIds = $this->resolveCuisineIds($validated, $entity->entityType);
        $foodPlaceFeatureIds = $this->resolveFoodPlaceFeatureIds($validated, $entity->entityType);
        $foodPlaceEntertainmentItemIds = $this->resolveFoodPlaceEntertainmentItemIds($validated, $entity->entityType);

        $entity->update([
            'entity_subtype_id' => $entitySubtypeId,
            'country_id' => $validated['country_id'],
            'place_id' => $validated['place_id'],
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'website' => $validated['website'] ?? null,
            'classification' => $classification,
        ]);
        $entity->features()->sync($featureIds);
        $entity->cuisines()->sync($cuisineIds);
        $entity->foodPlaceFeatures()->sync($foodPlaceFeatureIds);
        $entity->foodPlaceEntertainmentItems()->sync($foodPlaceEntertainmentItemIds);

        $entity->syncBulgarianTranslation(
            $validated['name'],
            $validated['address'] ?? null,
            $validated['description'] ?? null
        );

        return redirect()
            ->route('owner.entities.show', $entity)
            ->with('success', 'Обектът е обновен успешно.');
    }

    public function searchPlaces(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
        ]);

        $queryText = trim((string) ($validated['q'] ?? ''));
        if (mb_strlen($queryText) < 2) {
            return response()->json([]);
        }

        $query = Place::query()
            ->select(['id', 'name', 'type', 'municipality_name', 'region_name'])
            ->where('name', 'like', '%'.$queryText.'%');

        if (! empty($validated['country_id'])) {
            $query->where('country_id', $validated['country_id']);
        }

        return response()->json(
            $query->orderBy('name')
                ->limit(15)
                ->get()
        );
    }

    private function resolveEntityType(array $validated): EntityType
    {
        return EntityType::query()
            ->where('code', $validated['type'])
            ->firstOrFail();
    }

    private function resolveClassification(Request $request, string $entityTypeCode): ?string
    {
        if (! in_array($entityTypeCode, self::CLASSIFIABLE_TYPE_CODES, true)) {
            return null;
        }

        $classification = $request->input('classification');
        if ($classification === null || trim($classification) === '') {
            return null;
        }

        $validated = $request->validate([
            'classification' => ['string', 'max:255', Rule::in(self::ACCOMMODATION_CLASSIFICATIONS)],
        ]);

        return $validated['classification'];
    }

    private function resolveEntitySubtypeId(array $validated, int $entityTypeId): ?int
    {
        $subtypeId = $validated['entity_subtype_id'] ?? null;
        if (! $subtypeId) {
            return null;
        }

        $belongsToType = EntitySubtype::query()
            ->whereKey($subtypeId)
            ->where('entity_type_id', $entityTypeId)
            ->exists();

        if (! $belongsToType) {
            throw ValidationException::withMessages([
                'entity_subtype_id' => 'Избраният подтип не принадлежи към този тип обект.',
            ]);
        }

        return (int) $subtypeId;
    }

    private function authorizeOwnerEntityAccess(Request $request, Entity $entity): void
    {
        abort_if($entity->user_id !== $request->user()->id, 403);
    }

    private function loadTypeFeatures(EntityType $entityType)
    {
        if ($entityType->code !== self::FEATURED_TYPE_CODE) {
            return collect();
        }

        return EntityFeature::query()
            ->where('entity_type_id', $entityType->id)
            ->orderBy('feature_group')
            ->orderBy('name')
            ->get(['id', 'name', 'feature_group']);
    }

    private function resolveFeatureIds(array $validated, EntityType $entityType): array
    {
        if ($entityType->code !== self::FEATURED_TYPE_CODE) {
            return [];
        }

        $featureIds = array_values(array_unique($validated['feature_ids'] ?? []));
        if ($featureIds === []) {
            return [];
        }

        $validCount = EntityFeature::query()
            ->where('entity_type_id', $entityType->id)
            ->whereIn('id', $featureIds)
            ->count();

        if ($validCount !== count($featureIds)) {
            throw ValidationException::withMessages([
                'feature_ids' => 'Избрани са невалидни характеристики за този тип обект.',
            ]);
        }

        return array_map('intval', $featureIds);
    }

    private function loadTypeCuisines(EntityType $entityType)
    {
        if ($entityType->code !== self::CUISINE_TYPE_CODE) {
            return collect();
        }

        return FoodPlaceCuisine::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function resolveCuisineIds(array $validated, EntityType $entityType): array
    {
        if ($entityType->code !== self::CUISINE_TYPE_CODE) {
            return [];
        }

        $cuisineIds = array_values(array_unique($validated['cuisine_ids'] ?? []));
        if ($cuisineIds === []) {
            return [];
        }

        $validCount = FoodPlaceCuisine::query()
            ->whereIn('id', $cuisineIds)
            ->count();

        if ($validCount !== count($cuisineIds)) {
            throw ValidationException::withMessages([
                'cuisine_ids' => 'Избрани са невалидни кухни за този тип обект.',
            ]);
        }

        return array_map('intval', $cuisineIds);
    }

    private function loadFoodPlaceFeatureOptions(EntityType $entityType)
    {
        if ($entityType->code !== self::FOOD_PLACE_FEATURES_TYPE_CODE) {
            return collect();
        }

        return FoodPlaceFeature::query()
            ->orderBy('feature_group')
            ->orderBy('name')
            ->get(['id', 'name', 'feature_group']);
    }

    private function resolveFoodPlaceFeatureIds(array $validated, EntityType $entityType): array
    {
        if ($entityType->code !== self::FOOD_PLACE_FEATURES_TYPE_CODE) {
            return [];
        }

        $ids = array_values(array_unique($validated['food_place_feature_ids'] ?? []));
        if ($ids === []) {
            return [];
        }

        $validCount = FoodPlaceFeature::query()
            ->whereIn('id', $ids)
            ->count();

        if ($validCount !== count($ids)) {
            throw ValidationException::withMessages([
                'food_place_feature_ids' => 'Избрани са невалидни екстри за място за хранене.',
            ]);
        }

        return array_map('intval', $ids);
    }

    private function loadFoodPlaceEntertainmentOptions(EntityType $entityType)
    {
        if ($entityType->code !== self::FOOD_PLACE_ENTERTAINMENT_TYPE_CODE) {
            return collect();
        }

        return FoodPlaceEntertainmentItem::query()
            ->orderByRaw('CASE metadata_group WHEN ? THEN 0 WHEN ? THEN 1 WHEN ? THEN 2 ELSE 3 END', [
                FoodPlaceEntertainmentItem::GROUP_OFFERING,
                FoodPlaceEntertainmentItem::GROUP_GENRE_BALKAN,
                FoodPlaceEntertainmentItem::GROUP_GENRE_INTERNATIONAL,
            ])
            ->orderBy('name')
            ->get(['id', 'name', 'metadata_group']);
    }

    private function resolveFoodPlaceEntertainmentItemIds(array $validated, EntityType $entityType): array
    {
        if ($entityType->code !== self::FOOD_PLACE_ENTERTAINMENT_TYPE_CODE) {
            return [];
        }

        $ids = array_values(array_unique($validated['food_place_entertainment_item_ids'] ?? []));
        if ($ids === []) {
            return [];
        }

        $validCount = FoodPlaceEntertainmentItem::query()
            ->whereIn('id', $ids)
            ->count();

        if ($validCount !== count($ids)) {
            throw ValidationException::withMessages([
                'food_place_entertainment_item_ids' => 'Избрани са невалидни музикални метаданни за място за хранене.',
            ]);
        }

        return array_map('intval', $ids);
    }
}
