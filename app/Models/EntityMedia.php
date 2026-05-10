<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EntityMedia extends Model
{
    public const TYPE_IMAGE = 'image';

    public const TYPE_VIDEO = 'video';

    protected $table = 'entity_media';

    protected $fillable = [
        'entity_id',
        'type',
        'path',
        'url',
        'is_cover',
        'sort_order',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'is_cover' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function publicUrl(): ?string
    {
        if ($this->type !== self::TYPE_IMAGE || $this->path === null || $this->path === '') {
            return null;
        }

        return Storage::disk('public')->url($this->path);
    }
}
