<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Entity;
use App\Models\EntityFeature;
use App\Models\EntitySubtype;
use App\Models\EntityType;
use App\Models\FoodPlaceCuisine;
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

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'type' => ['nullable', 'integer', 'exists:entity_types,id'],
            'subtype' => ['nullable', 'integer', 'exists:entity_subtypes,id'],
            'status' => ['nullable', Rule::in(Entity::STATUSES)],
            'place' => ['nullable', 'string', 'max:255'],
            'owner' => ['nullable', 'string', 'max:255'],
        ]);

        // Global admin listing: intentionally no user_id ownership scope.
        $query = Entity::withoutGlobalScopes()
            ->with(['entityType', 'entitySubtype', 'place', 'user'])
            ->latest();

        if (! empty($validated['type'])) {
            $query->where('entity_type_id', $validated['type']);
        }

        if (! empty($validated['subtype'])) {
            $query->where('entity_subtype_id', $validated['subtype']);
        }

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $placeText = trim((string) ($validated['place'] ?? ''));
        if ($placeText !== '') {
            $query->whereHas('place', function ($q) use ($placeText): void {
                $q->where('name', 'like', '%'.$placeText.'%');
            });
        }

        $ownerText = trim((string) ($validated['owner'] ?? ''));
        if ($ownerText !== '') {
            $query->whereHas('user', function ($q) use ($ownerText): void {
                $q->where(function ($qq) use ($ownerText): void {
                    $qq->where('name', 'like', '%'.$ownerText.'%')
                        ->orWhere('email', 'like', '%'.$ownerText.'%');
                });
            });
        }

        return view('admin.entities.index', [
            'entities' => $query->paginate(20)->withQueryString(),
            'entityTypes' => EntityType::query()->orderBy('name')->get(['id', 'name']),
            'entitySubtypes' => EntitySubtype::query()->orderBy('name')->get(['id', 'name']),
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

        return view('admin.entities.create', [
            'entityType' => $entityType,
            'entitySubtypes' => EntitySubtype::query()
                ->where('entity_type_id', $entityType->id)
                ->orderBy('name')
                ->get(['id', 'name']),
            'entityFeatures' => $this->loadTypeFeatures($entityType),
            'foodPlaceCuisines' => $this->loadTypeCuisines($entityType),
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
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'classification' => ['nullable', 'string', 'max:255'],
            'feature_ids' => ['nullable', 'array'],
            'feature_ids.*' => ['integer'],
            'cuisine_ids' => ['nullable', 'array'],
            'cuisine_ids.*' => ['integer'],
        ]);

        $entityType = EntityType::query()
            ->where('code', $validated['type'])
            ->firstOrFail();
        $entitySubtypeId = $this->resolveEntitySubtypeId($validated, $entityType->id);
        $classification = $this->resolveClassification($request, $entityType->code);

        $entity = Entity::query()->create([
            'user_id' => null,
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
        $entity->features()->sync($this->resolveFeatureIds($validated, $entityType));
        $entity->cuisines()->sync($this->resolveCuisineIds($validated, $entityType));

        return redirect()
            ->route('admin.entities.show', $entity)
            ->with('success', 'Обектът е добавен успешно.');
    }

    public function show(Entity $entity): View
    {
        $entity->loadMissing(['entityType', 'entitySubtype', 'country', 'place', 'user', 'features', 'cuisines']);

        return view('admin.entities.show', [
            'entity' => $entity,
        ]);
    }

    public function edit(Entity $entity): View
    {
        $entity->loadMissing(['entityType', 'entitySubtype', 'country', 'place', 'user', 'features', 'cuisines']);

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

        return view('admin.entities.edit', [
            'entity' => $entity,
            'entityType' => $entity->entityType,
            'entitySubtypes' => EntitySubtype::query()
                ->where('entity_type_id', $entity->entity_type_id)
                ->orderBy('name')
                ->get(['id', 'name']),
            'entityFeatures' => $this->loadTypeFeatures($entity->entityType),
            'foodPlaceCuisines' => $this->loadTypeCuisines($entity->entityType),
            'countries' => Country::query()->orderBy('name')->get(['id', 'name']),
            'selectedPlace' => $selectedPlace,
        ]);
    }

    public function update(Request $request, Entity $entity): RedirectResponse
    {
        $validated = $request->validate([
            'entity_subtype_id' => ['nullable', 'integer', 'exists:entity_subtypes,id'],
            'status' => ['required', Rule::in(Entity::STATUSES)],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'place_id' => ['required', 'integer', 'exists:places,id'],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'classification' => ['nullable', 'string', 'max:255'],
            'feature_ids' => ['nullable', 'array'],
            'feature_ids.*' => ['integer'],
            'cuisine_ids' => ['nullable', 'array'],
            'cuisine_ids.*' => ['integer'],
        ]);

        $entitySubtypeId = $this->resolveEntitySubtypeId($validated, $entity->entity_type_id);
        $classification = $this->resolveClassification($request, $entity->entityType->code);

        $entity->update([
            'entity_subtype_id' => $entitySubtypeId,
            'status' => $validated['status'],
            'country_id' => $validated['country_id'],
            'place_id' => $validated['place_id'],
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'website' => $validated['website'] ?? null,
            'classification' => $classification,
        ]);
        $entity->features()->sync($this->resolveFeatureIds($validated, $entity->entityType));
        $entity->cuisines()->sync($this->resolveCuisineIds($validated, $entity->entityType));

        return redirect()
            ->route('admin.entities.show', $entity)
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
}

