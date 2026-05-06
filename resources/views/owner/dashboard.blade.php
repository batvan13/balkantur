@extends('layouts.app')

@section('title', __('Owner panel'))

@section('content')
    <div class="mx-auto max-w-5xl space-y-6 bg-white">
        @if (session('success'))
            <div class="rounded-lg border border-gray-400 p-4 text-sm text-black">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-gray-300 p-4">
            <h1 class="text-2xl font-semibold text-black">Owner panel</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                    Back to home
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="rounded-lg border border-gray-300 p-4">
            <p class="text-sm text-gray-700">Добавяне и управление на ваши туристически обекти.</p>
        </div>

        <section class="space-y-3">
            <h2 class="text-lg font-semibold text-black">Добави нов обект</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-gray-300 p-4">
                    <h3 class="font-medium text-black">Място за настаняване</h3>
                    <a href="{{ route('owner.entities.create', ['type' => 'accommodation']) }}" class="mt-3 inline-block rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                        Добави
                    </a>
                </div>

                <div class="rounded-lg border border-gray-300 p-4">
                    <h3 class="font-medium text-black">Място за хранене</h3>
                    <a href="{{ route('owner.entities.create', ['type' => 'food_place']) }}" class="mt-3 inline-block rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                        Добави
                    </a>
                </div>

                <div class="rounded-lg border border-gray-300 p-4">
                    <h3 class="font-medium text-black">Атракция</h3>
                    <a href="{{ route('owner.entities.create', ['type' => 'attraction']) }}" class="mt-3 inline-block rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                        Добави
                    </a>
                </div>
            </div>
        </section>

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
