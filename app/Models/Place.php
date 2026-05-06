<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    protected $fillable = [
        'country_id',
        'name',
        'type',
        'ekatte_code',
        'municipality_name',
        'region_name',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function entities(): HasMany
    {
        return $this->hasMany(Entity::class);
    }
}
