<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FoodPlaceFeature extends Model
{
    public const GROUP_FACILITY = 'facility';

    public const GROUP_SERVICE = 'service';

    public const GROUP_ENTERTAINMENT = 'entertainment';

    public const GROUP_PAYMENT_ACCESS = 'payment_access';

    /** @var list<string> */
    public const GROUPS = [
        self::GROUP_FACILITY,
        self::GROUP_SERVICE,
        self::GROUP_ENTERTAINMENT,
        self::GROUP_PAYMENT_ACCESS,
    ];

    protected $fillable = [
        'code',
        'name',
        'feature_group',
    ];

    public function entities(): BelongsToMany
    {
        return $this->belongsToMany(Entity::class, 'entity_food_place_feature')
            ->withTimestamps();
    }
}
