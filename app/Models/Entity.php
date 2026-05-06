<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entity extends Model
{
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
}

