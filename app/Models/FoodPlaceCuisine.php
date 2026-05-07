<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FoodPlaceCuisine extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    public function entities(): BelongsToMany
    {
        return $this->belongsToMany(Entity::class, 'entity_food_place_cuisine')
            ->withTimestamps();
    }
}
