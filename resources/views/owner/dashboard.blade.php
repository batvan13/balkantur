@extends('layouts.app')

@section('title', __('Owner panel'))

@section('content')
    <div class="relative left-1/2 w-[min(96vw,1280px)] -translate-x-1/2 space-y-6 bg-white px-2">
        @if (session('success'))
            <div class="rounded-lg border border-gray-400 p-4 text-sm text-black">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-lg border border-gray-300 p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-semibold text-black">Работен панел</h1>
                    <p class="mt-1 text-sm text-gray-700">Управлявайте вашите туристически обекти от едно място.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('home') }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                        Към сайта
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                            Изход
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded border border-gray-300 p-3">
                    <p class="text-xs text-gray-600">Всички обекти</p>
                    <p class="mt-1 text-xl font-semibold text-black">{{ $entities->count() }}</p>
                </div>
                <div class="rounded border border-gray-300 p-3">
                    <p class="text-xs text-gray-600">Чернови</p>
                    <p class="mt-1 text-xl font-semibold text-black">{{ $entities->where('status', 'draft')->count() }}</p>
                </div>
                <div class="rounded border border-gray-300 p-3">
                    <p class="text-xs text-gray-600">Публикувани</p>
                    <p class="mt-1 text-xl font-semibold text-black">{{ $entities->where('status', 'published')->count() }}</p>
                </div>
                <div class="rounded border border-gray-300 p-3">
                    <p class="text-xs text-gray-600">Скрити</p>
                    <p class="mt-1 text-xl font-semibold text-black">{{ $entities->where('status', 'hidden')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-gray-300 p-4">
            <div class="text-sm text-gray-700">Бързо добавяне по тип:</div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('owner.entities.create', ['type' => 'accommodation']) }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                    Място за настаняване
                </a>
                <a href="{{ route('owner.entities.create', ['type' => 'food_place']) }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                    Място за хранене
                </a>
                <a href="{{ route('owner.entities.create', ['type' => 'attraction']) }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                    Атракция
                </a>
            </div>
        </div>

        <section class="space-y-3">
            <h2 class="text-lg font-semibold text-black">Моите обекти</h2>
            @if ($entities->isEmpty())
                <div class="rounded-lg border border-gray-300 p-4">
                    <p class="text-sm text-gray-700">Все още нямате добавени обекти.</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-lg border border-gray-300">
                    <table class="min-w-full divide-y divide-gray-300 text-sm">
                        <thead class="bg-gray-50 text-left text-gray-700">
                            <tr>
                                <th class="px-3 py-2 font-medium">Име</th>
                                <th class="px-3 py-2 font-medium">Тип</th>
                                <th class="px-3 py-2 font-medium">Подтип</th>
                                <th class="px-3 py-2 font-medium">Статус</th>
                                <th class="px-3 py-2 font-medium">Категоризация</th>
                                <th class="px-3 py-2 font-medium">Населено място</th>
                                <th class="px-3 py-2 font-medium">Телефон</th>
                                <th class="px-3 py-2 font-medium">Уебсайт</th>
                                <th class="px-3 py-2 font-medium">Действие</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white text-black">
                            @foreach ($entities as $entity)
                                <tr>
                                    <td class="px-3 py-2">{{ $entity->name }}</td>
                                    <td class="px-3 py-2">{{ $entity->entityType?->name ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $entity->entitySubtype?->name ?? '-' }}</td>
                                    <td class="px-3 py-2">
                                        @if ($entity->status === 'draft')
                                            Чернова
                                        @elseif ($entity->status === 'published')
                                            Публикуван
                                        @elseif ($entity->status === 'hidden')
                                            Скрит
                                        @else
                                            {{ $entity->status ?: '-' }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">{{ $entity->classification ?: '-' }}</td>
                                    <td class="px-3 py-2">{{ $entity->place?->name ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $entity->phone ?: '-' }}</td>
                                    <td class="px-3 py-2">
                                        @if ($entity->website)
                                            <a href="{{ $entity->website }}" target="_blank" rel="noopener noreferrer" class="underline">
                                                {{ $entity->website }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('owner.entities.show', $entity) }}" class="rounded border border-gray-400 px-3 py-1 text-sm text-black hover:bg-gray-100">
                                                Преглед
                                            </a>
                                            <a href="{{ route('owner.entities.edit', $entity) }}" class="rounded border border-gray-400 px-3 py-1 text-sm text-black hover:bg-gray-100">
                                                Редакция
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
