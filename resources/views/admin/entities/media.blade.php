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

        <div class="rounded-lg border border-gray-300 p-4">
            <h2 class="mb-3 text-sm font-semibold text-black">Текущи медии</h2>
            @if ($entity->entityMedia->isEmpty())
                <p class="text-sm text-black">Няма качени медии.</p>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach ($entity->entityMedia as $m)
                        <div class="flex flex-wrap items-start gap-4 py-4">
                            <div class="text-xs text-gray-600">
                                #{{ $m->sort_order }}
                                @if ($m->type === \App\Models\EntityMedia::TYPE_IMAGE)
                                    · снимка
                                    @if ($m->is_cover)
                                        · <span class="font-medium text-black">главна</span>
                                    @endif
                                @else
                                    · видео линк
                                @endif
                            </div>
                            <div class="flex-1 min-w-[12rem]">
                                @if ($m->type === \App\Models\EntityMedia::TYPE_IMAGE && $m->publicUrl())
                                    <img src="{{ $m->publicUrl() }}" alt="" class="max-h-28 rounded border border-gray-300">
                                @elseif ($m->type === \App\Models\EntityMedia::TYPE_VIDEO && $m->url)
                                    <a href="{{ $m->url }}" target="_blank" rel="noopener noreferrer" class="break-all text-sm underline">{{ $m->url }}</a>
                                @else
                                    <span class="text-sm text-black">—</span>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @if ($m->type === \App\Models\EntityMedia::TYPE_IMAGE && ! $m->is_cover)
                                    <form method="POST" action="{{ route('admin.entities.media.cover', [$entity, $m]) }}">
                                        @csrf
                                        <button type="submit" class="rounded border border-gray-400 px-2 py-1 text-xs text-black hover:bg-gray-100">
                                            Главна снимка
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.entities.media.destroy', [$entity, $m]) }}" onsubmit="return confirm('Премахване на този запис?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded border border-gray-400 px-2 py-1 text-xs text-black hover:bg-gray-100">
                                        Изтрий
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
