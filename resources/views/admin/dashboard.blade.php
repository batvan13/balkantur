@extends('layouts.admin')

@section('title', __('Super admin'))
@section('admin_page_title', 'Super Admin Panel')

@section('content')
    <div class="rounded-lg border border-gray-300 bg-white p-6">
        <h2 class="text-xl font-semibold text-black">Super Admin Panel</h2>
        <p class="mt-2 text-sm text-gray-700">Централно управление на платформата.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-lg border border-gray-300 bg-white p-4">
            <p class="text-sm text-gray-700">Общ брой обекти</p>
            <p class="mt-2 text-2xl font-semibold text-black">{{ $totalEntities }}</p>
        </div>
        <div class="rounded-lg border border-gray-300 bg-white p-4">
            <p class="text-sm text-gray-700">Брой места за настаняване</p>
            <p class="mt-2 text-2xl font-semibold text-black">{{ $accommodationCount }}</p>
        </div>
        <div class="rounded-lg border border-gray-300 bg-white p-4">
            <p class="text-sm text-gray-700">Брой места за хранене</p>
            <p class="mt-2 text-2xl font-semibold text-black">{{ $foodPlaceCount }}</p>
        </div>
        <div class="rounded-lg border border-gray-300 bg-white p-4">
            <p class="text-sm text-gray-700">Брой атракции</p>
            <p class="mt-2 text-2xl font-semibold text-black">{{ $attractionCount }}</p>
        </div>
        <div class="rounded-lg border border-gray-300 bg-white p-4">
            <p class="text-sm text-gray-700">Брой обекти без собственик</p>
            <p class="mt-2 text-2xl font-semibold text-black">{{ $withoutOwnerCount }}</p>
        </div>
    </div>

    <div class="rounded-lg border border-gray-300 bg-white p-4">
        <h3 class="text-lg font-semibold text-black">Последно добавени обекти</h3>

        <div class="mt-3 overflow-x-auto rounded-lg border border-gray-300">
            <table class="min-w-full divide-y divide-gray-300 text-sm">
                <thead class="bg-gray-50 text-left text-gray-700">
                    <tr>
                        <th class="px-3 py-2 font-medium">Име</th>
                        <th class="px-3 py-2 font-medium">Тип</th>
                        <th class="px-3 py-2 font-medium">Населено място</th>
                        <th class="px-3 py-2 font-medium">Собственик</th>
                        <th class="px-3 py-2 font-medium">Действие</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white text-black">
                    @forelse ($recentEntities as $entity)
                        <tr>
                            <td class="px-3 py-2">{{ $entity->name }}</td>
                            <td class="px-3 py-2">{{ $entity->entityType?->name ?? '-' }}</td>
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
                                <a href="{{ route('admin.entities.show', $entity) }}" class="rounded border border-gray-400 px-3 py-1 text-sm text-black hover:bg-gray-100">
                                    Преглед
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-center text-gray-700">Няма обекти.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
