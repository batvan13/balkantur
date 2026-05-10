<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\EntityMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EntityMediaController extends Controller
{
    public function edit(Request $request, Entity $entity): View
    {
        $this->authorizeOwnerEntityAccess($request, $entity);

        $entity->load(['entityMedia']);

        return view('owner.entities.media', [
            'entity' => $entity,
        ]);
    }

    public function storeImages(Request $request, Entity $entity): RedirectResponse
    {
        $this->authorizeOwnerEntityAccess($request, $entity);

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
            ->route('owner.entities.media.edit', $entity)
            ->with('success', 'Снимките са качени.');
    }

    public function storeVideo(Request $request, Entity $entity): RedirectResponse
    {
        $this->authorizeOwnerEntityAccess($request, $entity);

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
            ->route('owner.entities.media.edit', $entity)
            ->with('success', 'Видео линкът е добавен.');
    }

    public function setCover(Request $request, Entity $entity, EntityMedia $media): RedirectResponse
    {
        $this->authorizeOwnerEntityAccess($request, $entity);

        abort_if($media->entity_id !== $entity->id, 404);
        abort_if($media->type !== EntityMedia::TYPE_IMAGE, 403);

        EntityMedia::query()
            ->where('entity_id', $entity->id)
            ->where('type', EntityMedia::TYPE_IMAGE)
            ->update(['is_cover' => false]);

        $media->update(['is_cover' => true]);

        return redirect()
            ->route('owner.entities.media.edit', $entity)
            ->with('success', 'Главната снимка е обновена.');
    }

    public function destroy(Request $request, Entity $entity, EntityMedia $media): RedirectResponse
    {
        $this->authorizeOwnerEntityAccess($request, $entity);

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
            ->route('owner.entities.media.edit', $entity)
            ->with('success', 'Медията е премахната.');
    }

    private function authorizeOwnerEntityAccess(Request $request, Entity $entity): void
    {
        abort_if($entity->user_id !== $request->user()->id, 403);
    }
}
