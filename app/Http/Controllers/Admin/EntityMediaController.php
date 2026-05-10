<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\EntityMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EntityMediaController extends Controller
{
    public function edit(Entity $entity): View
    {
        $entity->load(['entityMedia']);

        return view('admin.entities.media', [
            'entity' => $entity,
        ]);
    }

    public function storeImages(Request $request, Entity $entity): RedirectResponse
    {
        $validated = $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'max:5120'],
        ]);

        $needsCover = ! EntityMedia::query()
            ->where('entity_id', $entity->id)
            ->where('type', EntityMedia::TYPE_IMAGE)
            ->where('is_cover', true)
            ->exists();

        $disk = Storage::disk('public');
        $nextSort = (int) EntityMedia::query()->where('entity_id', $entity->id)->max('sort_order');

        foreach ($validated['images'] as $index => $file) {
            $path = $disk->putFile('entities/'.$entity->id, $file);
            $nextSort++;
            $isCover = $needsCover && $index === 0;

            EntityMedia::query()->create([
                'entity_id' => $entity->id,
                'type' => EntityMedia::TYPE_IMAGE,
                'path' => $path,
                'url' => null,
                'is_cover' => $isCover,
                'sort_order' => $nextSort,
            ]);
        }

        return redirect()
            ->route('admin.entities.media.edit', $entity)
            ->with('success', 'Снимките са качени.');
    }

    public function storeVideo(Request $request, Entity $entity): RedirectResponse
    {
        $validated = $request->validate([
            'video_url' => ['required', 'url', 'max:2048'],
        ]);

        $sort = (int) EntityMedia::query()->where('entity_id', $entity->id)->max('sort_order') + 1;

        EntityMedia::query()->create([
            'entity_id' => $entity->id,
            'type' => EntityMedia::TYPE_VIDEO,
            'path' => null,
            'url' => $validated['video_url'],
            'is_cover' => false,
            'sort_order' => $sort,
        ]);

        return redirect()
            ->route('admin.entities.media.edit', $entity)
            ->with('success', 'Видео линкът е добавен.');
    }

    public function setCover(Entity $entity, EntityMedia $media): RedirectResponse
    {
        abort_if($media->entity_id !== $entity->id, 404);
        abort_if($media->type !== EntityMedia::TYPE_IMAGE, 403);

        EntityMedia::query()
            ->where('entity_id', $entity->id)
            ->where('type', EntityMedia::TYPE_IMAGE)
            ->update(['is_cover' => false]);

        $media->update(['is_cover' => true]);

        return redirect()
            ->route('admin.entities.media.edit', $entity)
            ->with('success', 'Главната снимка е обновена.');
    }

    public function destroy(Entity $entity, EntityMedia $media): RedirectResponse
    {
        abort_if($media->entity_id !== $entity->id, 404);

        if ($media->type === EntityMedia::TYPE_IMAGE && $media->path) {
            Storage::disk('public')->delete($media->path);
        }

        $wasCover = $media->is_cover && $media->type === EntityMedia::TYPE_IMAGE;
        $media->delete();

        if ($wasCover) {
            $first = EntityMedia::query()
                ->where('entity_id', $entity->id)
                ->where('type', EntityMedia::TYPE_IMAGE)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();

            if ($first) {
                EntityMedia::query()
                    ->where('entity_id', $entity->id)
                    ->where('type', EntityMedia::TYPE_IMAGE)
                    ->update(['is_cover' => false]);
                $first->update(['is_cover' => true]);
            }
        }

        return redirect()
            ->route('admin.entities.media.edit', $entity)
            ->with('success', 'Медията е премахната.');
    }
}
