<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FoodPlaceEntertainmentItem extends Model
{
    public const GROUP_OFFERING = 'offering';

    public const GROUP_GENRE_BALKAN = 'genre_balkan';

    public const GROUP_GENRE_INTERNATIONAL = 'genre_international';

    /** @var list<string> */
    public const GROUPS = [
        self::GROUP_OFFERING,
        self::GROUP_GENRE_BALKAN,
        self::GROUP_GENRE_INTERNATIONAL,
    ];

    protected $fillable = [
        'code',
        'name',
        'metadata_group',
    ];

    public function entities(): BelongsToMany
    {
        return $this->belongsToMany(Entity::class, 'entity_food_place_entertainment_item')
            ->withTimestamps();
    }
}
