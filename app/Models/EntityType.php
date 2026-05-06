<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntityType extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    public function entities(): HasMany
    {
        return $this->hasMany(Entity::class);
    }

    public function subtypes(): HasMany
    {
        return $this->hasMany(EntitySubtype::class);
    }
}

