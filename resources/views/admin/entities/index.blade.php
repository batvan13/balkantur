@extends('layouts.admin')

@section('title', 'Admin entities')
@section('admin_page_title', 'Обекти')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6 bg-white">
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-gray-300 p-4">
            <h1 class="text-2xl font-semibold text-black">Админ: Обекти</h1>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.entities.create', ['type' => 'accommodation']) }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                    Добави място за настаняване
                </a>
                <a href="{{ route('admin.entities.create', ['type' => 'food_place']) }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                    Добави място за хранене
                </a>
                <a href="{{ route('admin.entities.create', ['type' => 'attraction']) }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                    Добави атракция
                </a>
                <a href="{{ route('admin.dashboard') }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                    Back to admin dashboard
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.entities.index') }}" class="grid gap-3 rounded-lg border border-gray-300 p-4 md:grid-cols-4">
            <div class="space-y-1">
                <label for="type" class="block text-sm font-medium text-black">Тип</label>
                <select id="type" name="type" class="w-full rounded border border-gray-400 px-3 py-2 text-sm text-black">
                    <option value="">Всички</option>
                    @foreach ($entityTypes as $entityType)
                        <option value="{{ $entityType->id }}" @selected((string) request('type') === (string) $entityType->id)>
                            {{ $entityType->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label for="subtype" class="block text-sm font-medium text-black">Подтип</label>
                <select id="subtype" name="subtype" class="w-full rounded border border-gray-400 px-3 py-2 text-sm text-black">
                    <option value="">Всички</option>
                    @foreach ($entitySubtypes as $entitySubtype)
                        <option value="{{ $entitySubtype->id }}" @selected((string) request('subtype') === (string) $entitySubtype->id)>
                            {{ $entitySubtype->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label for="place" class="block text-sm font-medium text-black">Населено място</label>
                <input id="place" name="place" type="text" value="{{ request('place') }}" class="w-full rounded border border-gray-400 px-3 py-2 text-sm text-black">
            </div>

            <div class="space-y-1">
                <label for="owner" class="block text-sm font-medium text-black">Собственик</label>
                <input id="owner" name="owner" type="text" value="{{ request('owner') }}" class="w-full rounded border border-gray-400 px-3 py-2 text-sm text-black">
            </div>

            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="rounded border border-gray-400 px-4 py-2 text-sm text-black hover:bg-gray-100">
                    Филтрирай
                </button>
                <a href="{{ route('admin.entities.index') }}" class="rounded border border-gray-400 px-4 py-2 text-sm text-black hover:bg-gray-100">
                    Изчисти
                </a>
            </div>
        </form>

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
                        <th class="px-3 py-2 font-medium">Собственик</th>
                        <th class="px-3 py-2 font-medium">Действие</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white text-black">
                    @forelse ($entities as $entity)
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
                            <td class="px-3 py-2">
                                @if ($entity->user)
                                    <div>{{ $entity->user->name }}</div>
                                    <div class="text-xs text-gray-600">{{ $entity->user->email }}</div>
                                @else
                                    Системен / без собственик
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.entities.show', $entity) }}" class="rounded border border-gray-400 px-3 py-1 text-sm text-black hover:bg-gray-100">
                                        Преглед
                                    </a>
                                    <a href="{{ route('admin.entities.edit', $entity) }}" class="rounded border border-gray-400 px-3 py-1 text-sm text-black hover:bg-gray-100">
                                        Редакция
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-4 text-center text-gray-700">Няма намерени обекти.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $entities->links() }}
        </div>
    </div>
@endsection

