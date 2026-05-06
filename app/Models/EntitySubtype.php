<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntitySubtype extends Model
{
    protected $fillable = [
        'entity_type_id',
        'name',
        'code',
    ];

    public function entityType(): BelongsTo
    {
        return $this->belongsTo(EntityType::class);
    }

    public function entities(): HasMany
    {
        return $this->hasMany(Entity::class);
    }
}

