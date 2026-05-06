<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    public function places(): HasMany
    {
        return $this->hasMany(Place::class);
    }

    public function entities(): HasMany
    {
        return $this->hasMany(Entity::class);
    }
}
