<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Entity extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_HIDDEN = 'hidden';

    /** @var list<string> */
    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PUBLISHED,
        self::STATUS_HIDDEN,
    ];

    protected $fillable = [
        'user_id',
        'entity_type_id',
        'entity_subtype_id',
        'country_id',
        'place_id',
        'name',
        'address',
        'phone',
        'email',
        'website',
        'classification',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entityType(): BelongsTo
    {
        return $this->belongsTo(EntityType::class);
    }

    public function entitySubtype(): BelongsTo
    {
        return $this->belongsTo(EntitySubtype::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(EntityFeature::class, 'entity_entity_feature')
            ->withTimestamps();
    }

    public function cuisines(): BelongsToMany
    {
        return $this->belongsToMany(FoodPlaceCuisine::class, 'entity_food_place_cuisine')
            ->withTimestamps();
    }

    public function foodPlaceFeatures(): BelongsToMany
    {
        return $this->belongsToMany(FoodPlaceFeature::class, 'entity_food_place_feature')
            ->withTimestamps();
    }

    public function foodPlaceEntertainmentItems(): BelongsToMany
    {
        return $this->belongsToMany(FoodPlaceEntertainmentItem::class, 'entity_food_place_entertainment_item')
            ->withTimestamps();
    }

    public function entityMedia(): HasMany
    {
        return $this->hasMany(EntityMedia::class)->orderBy('sort_order')->orderBy('id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(EntityTranslation::class);
    }

    public function bgTranslation(): HasOne
    {
        return $this->hasOne(EntityTranslation::class)
            ->where('locale', EntityTranslation::LOCALE_BG);
    }

    /**
     * Phase 1: keep entities.name / entities.address in sync with the Bulgarian translation row.
     */
    public function syncBulgarianTranslation(string $name, ?string $address, ?string $description): void
    {
        $normalizedDescription = $description !== null && trim($description) !== '' ? trim($description) : null;

        $this->translations()->updateOrCreate(
            ['locale' => EntityTranslation::LOCALE_BG],
            [
                'name' => $name,
                'address' => $address,
                'description' => $normalizedDescription,
            ]
        );
    }
}
