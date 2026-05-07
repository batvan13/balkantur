<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EntityFeature extends Model
{
    public const GROUP_FACILITY = 'facility';

    public const GROUP_POLICY = 'policy';

    /** @var list<string> */
    public const GROUPS = [
        self::GROUP_FACILITY,
        self::GROUP_POLICY,
    ];

    protected $fillable = [
        'entity_type_id',
        'code',
        'name',
        'feature_group',
    ];

    public function entityType(): BelongsTo
    {
        return $this->belongsTo(EntityType::class);
    }

    public function entities(): BelongsToMany
    {
        return $this->belongsToMany(Entity::class, 'entity_entity_feature')
            ->withTimestamps();
    }
}
