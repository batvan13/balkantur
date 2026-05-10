@extends('layouts.admin')

@section('title', 'Медия (обект)')
@section('admin_page_title', 'Медия на обект')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6 bg-white">
        @if (session('success'))
            <div class="rounded-lg border border-gray-400 p-4 text-sm text-black">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-gray-300 p-4">
            <h1 class="text-2xl font-semibold text-black">Медия: {{ $entity->name }}</h1>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.entities.show', $entity) }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                    Към прегледа
                </a>
                <a href="{{ route('admin.entities.edit', $entity) }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                    Редакция на данни
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-lg border border-gray-400 p-4">
                <ul class="list-disc space-y-1 pl-5 text-sm text-black">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="space-y-6 rounded-lg border border-gray-300 p-4">
            <div class="space-y-2">
                <h2 class="text-sm font-semibold text-black">Качи снимки</h2>
                <form method="POST" action="{{ route('admin.entities.media.images.store', $entity) }}" enctype="multipart/form-data" class="space-y-2">
                    @csrf
                    <input type="file" name="images[]" accept="image/*" multiple required class="block text-sm text-black">
                    @error('images')
                        <p class="text-sm text-black">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="text-sm text-black">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                        Качи
                    </button>
                </form>
            </div>

            <div class="space-y-2">
                <h2 class="text-sm font-semibold text-black">Добави видео (URL)</h2>
                <form method="POST" action="{{ route('admin.entities.media.video.store', $entity) }}" class="flex flex-wrap items-end gap-2">
                    @csrf
                    <div class="min-w-[min(100%,20rem)] flex-1 space-y-1">
                        <label for="video_url" class="block text-sm font-medium text-black">Линк</label>
                        <input id="video_url" name="video_url" type="url" value="{{ old('video_url') }}" required class="w-full rounded border border-gray-400 px-3 py-2 text-sm text-black">
                    </div>
                    <button type="submit" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                        Добави
                    </button>
                </form>
                @error('video_url')
                    <p class="text-sm text-black">{{ $message }}</p>
                @enderror
            </div>
        </div>

        @php
            $mediaImages = $entity->entityMedia->where('type', \App\Models\EntityMedia::TYPE_IMAGE)->values();
            $mediaVideos = $entity->entityMedia->where('type', \App\Models\EntityMedia::TYPE_VIDEO)->values();
        @endphp

        <div class="rounded-lg border border-gray-300 p-4 space-y-8">
            <h2 class="text-sm font-semibold text-black">Текущи медии</h2>

            @if ($entity->entityMedia->isEmpty())
                <p class="text-sm text-black">Няма качени снимки или видеа.</p>
            @else
                <div class="space-y-3">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-700">Снимки</h3>
                    @if ($mediaImages->isEmpty())
                        <p class="text-sm text-black">Няма качени снимки.</p>
                    @else
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($mediaImages as $m)
                                <div class="flex flex-col rounded border border-gray-300 bg-white {{ $m->is_cover ? 'ring-2 ring-gray-600' : '' }}">
                                    <div class="relative aspect-[4/3] w-full overflow-hidden bg-gray-100">
                                        @if ($m->publicUrl())
                                            <img src="{{ $m->publicUrl() }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full items-center justify-center text-xs text-gray-500">Няма преглед</div>
                                        @endif
                                        @if ($m->is_cover)
                                            <span class="absolute left-2 top-2 rounded bg-black px-2 py-0.5 text-xs font-medium text-white">Главна снимка</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-col gap-2 border-t border-gray-200 p-3">
                                        <p class="text-xs text-gray-600">Ред #{{ $m->sort_order }}</p>
                                        <div class="flex flex-col gap-2">
                                            @if (! $m->is_cover)
                                                <form method="POST" action="{{ route('admin.entities.media.cover', [$entity, $m]) }}">
                                                    @csrf
                                                    <button type="submit" class="w-full rounded border border-gray-400 px-2 py-1.5 text-xs text-black hover:bg-gray-100">
                                                        Направи главна
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('admin.entities.media.destroy', [$entity, $m]) }}" onsubmit="return confirm('Изтриване на тази снимка?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full rounded border border-gray-400 px-2 py-1.5 text-xs text-black hover:bg-gray-100">
                                                    Изтрий
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="space-y-3 border-t border-gray-200 pt-6">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-700">Видео линкове</h3>
                    @if ($mediaVideos->isEmpty())
                        <p class="text-sm text-black">Няма добавени видео линкове.</p>
                    @else
                        <ul class="divide-y divide-gray-200 rounded border border-gray-300">
                            @foreach ($mediaVideos as $m)
                                <li class="flex flex-wrap items-start gap-3 p-3">
                                    <div class="min-w-0 flex-1">
                                        <p class="mb-1 text-xs text-gray-600">Видео · ред #{{ $m->sort_order }}</p>
                                        @if ($m->url)
                                            <a href="{{ $m->url }}" target="_blank" rel="noopener noreferrer" class="break-all text-sm text-black underline">{{ $m->url }}</a>
                                        @else
                                            <span class="text-sm text-black">—</span>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('admin.entities.media.destroy', [$entity, $m]) }}" onsubmit="return confirm('Изтриване на този видео линк?');" class="shrink-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded border border-gray-400 px-2 py-1.5 text-xs text-black hover:bg-gray-100">
                                            Изтрий
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection
