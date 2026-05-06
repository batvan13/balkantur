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
            </div>
        </div>

        <div class="rounded-lg border border-gray-300">
            <div class="grid gap-0 divide-y divide-gray-200 text-sm">
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3"><div class="font-medium text-gray-700">Име</div><div class="md:col-span-2 text-black">{{ $entity->name ?: '-' }}</div></div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3"><div class="font-medium text-gray-700">Тип</div><div class="md:col-span-2 text-black">{{ $entity->entityType?->name ?? '-' }}</div></div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3"><div class="font-medium text-gray-700">Подтип</div><div class="md:col-span-2 text-black">{{ $entity->entitySubtype?->name ?? '-' }}</div></div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3"><div class="font-medium text-gray-700">Статус</div><div class="md:col-span-2 text-black">@if ($entity->status === 'draft') Чернова @elseif ($entity->status === 'published') Публикуван @elseif ($entity->status === 'hidden') Скрит @else {{ $entity->status ?: '-' }} @endif</div></div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3"><div class="font-medium text-gray-700">Категоризация</div><div class="md:col-span-2 text-black">{{ $entity->classification ?: '-' }}</div></div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3"><div class="font-medium text-gray-700">Населено място</div><div class="md:col-span-2 text-black">{{ $entity->place?->name ?? '-' }}</div></div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3"><div class="font-medium text-gray-700">Собственик</div><div class="md:col-span-2 text-black">@if($entity->user){{ $entity->user->name }} ({{ $entity->user->email }})@else Системен / без собственик @endif</div></div>
            </div>
        </div>
    </div>
@endsection

