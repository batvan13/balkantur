@extends('layouts.admin')

@section('title', 'Admin entity preview')
@section('admin_page_title', 'Преглед на обект')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6 bg-white">
        @if (session('success'))
            <div class="rounded-lg border border-gray-400 p-4 text-sm text-black">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-gray-300 p-4">
            <h1 class="text-2xl font-semibold text-black">Преглед на обект</h1>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.entities.index') }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                    Back to entities
                </a>
                <a href="{{ route('admin.entities.edit', $entity) }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                    Редакция
                </a>
                <a href="{{ route('admin.entities.media.edit', $entity) }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                    Медия
                </a>
            </div>
        </div>

        <div class="rounded-lg border border-gray-300">
            <div class="grid gap-0 divide-y divide-gray-200 text-sm">
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3"><div class="font-medium text-gray-700">Име</div><div class="md:col-span-2 text-black">{{ $entity->name ?: '-' }}</div></div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3"><div class="font-medium text-gray-700">Тип</div><div class="md:col-span-2 text-black">{{ $entity->entityType?->name ?? '-' }}</div></div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3"><div class="font-medium text-gray-700">Подтип</div><div class="md:col-span-2 text-black">{{ $entity->entitySubtype?->name ?? '-' }}</div></div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3"><div class="font-medium text-gray-700">Статус</div><div class="md:col-span-2 text-black">@if ($entity->status === 'draft') Чернова @elseif ($entity->status === 'published') Публикуван @elseif ($entity->status === 'hidden') Скрит @else {{ $entity->status ?: '-' }} @endif</div></div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3"><div class="font-medium text-gray-700">Категоризация</div><div class="md:col-span-2 text-black">{{ $entity->classification ?: '-' }}</div></div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3"><div class="font-medium text-gray-700">Описание</div><div class="md:col-span-2 text-black whitespace-pre-wrap break-words">{{ ($entity->bgTranslation?->description) ?: '-' }}</div></div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3"><div class="font-medium text-gray-700">Населено място</div><div class="md:col-span-2 text-black">{{ $entity->place?->name ?? '-' }}</div></div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3"><div class="font-medium text-gray-700">Собственик</div><div class="md:col-span-2 text-black">@if($entity->user){{ $entity->user->name }} ({{ $entity->user->email }})@else Системен / без собственик @endif</div></div>

                @if (($entity->entityType?->code ?? '') === 'accommodation')
                    <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3">
                        <div class="font-medium text-gray-700">Характеристики на настаняване</div>
                        <div class="md:col-span-2 text-black">
                            @if ($entity->features->isEmpty())
                                -
                            @else
                                @foreach ($entity->features->groupBy('feature_group') as $groupCode => $items)
                                    <div class="mb-2 last:mb-0">
                                        <span class="font-medium text-gray-800">
                                            @switch ($groupCode)
                                                @case ('facility')
                                                    Удобства
                                                    @break
                                                @case ('policy')
                                                    Политики
                                                    @break
                                                @default
                                                    {{ $groupCode }}
                                            @endswitch
                                            :
                                        </span>
                                        {{ $items->sortBy('name')->pluck('name')->implode(', ') }}
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif

                @if (($entity->entityType?->code ?? '') === 'food_place')
                    <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3">
                        <div class="font-medium text-gray-700">Тип кухня</div>
                        <div class="md:col-span-2 text-black">
                            {{ $entity->cuisines->isEmpty() ? '-' : $entity->cuisines->sortBy('name')->pluck('name')->implode(', ') }}
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3">
                        <div class="font-medium text-gray-700">Екстри / услуги</div>
                        <div class="md:col-span-2 text-black">
                            @if ($entity->foodPlaceFeatures->isEmpty())
                                -
                            @else
                                @foreach ($entity->foodPlaceFeatures->groupBy('feature_group') as $groupCode => $items)
                                    <div class="mb-2 last:mb-0">
                                        <span class="font-medium text-gray-800">
                                            @switch ($groupCode)
                                                @case ('facility')
                                                    Удобства
                                                    @break
                                                @case ('service')
                                                    Услуги
                                                    @break
                                                @case ('entertainment')
                                                    Развлечения
                                                    @break
                                                @case ('payment_access')
                                                    Плащане и достъп
                                                    @break
                                                @default
                                                    {{ $groupCode }}
                                            @endswitch
                                            :
                                        </span>
                                        {{ $items->sortBy('name')->pluck('name')->implode(', ') }}
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3">
                        <div class="font-medium text-gray-700">Музика и забавление (метаданни)</div>
                        <div class="md:col-span-2 text-black">
                            @if ($entity->foodPlaceEntertainmentItems->isEmpty())
                                -
                            @else
                                @foreach ($entity->foodPlaceEntertainmentItems->groupBy('metadata_group') as $groupCode => $items)
                                    <div class="mb-2 last:mb-0">
                                        <span class="font-medium text-gray-800">
                                            @switch ($groupCode)
                                                @case ('offering')
                                                    Формати / предлагане
                                                    @break
                                                @case ('genre_balkan')
                                                    Балканска и регионална музика
                                                    @break
                                                @case ('genre_international')
                                                    Международни стилове
                                                    @break
                                                @default
                                                    {{ $groupCode }}
                                            @endswitch
                                            :
                                        </span>
                                        {{ $items->sortBy('name')->pluck('name')->implode(', ') }}
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

