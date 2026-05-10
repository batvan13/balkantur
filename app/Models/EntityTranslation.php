<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntityTranslation extends Model
{
    public const LOCALE_BG = 'bg';

    protected $fillable = [
        'entity_id',
        'locale',
        'name',
        'address',
        'description',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }
}
