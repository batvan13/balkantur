@extends('layouts.app')

@section('title', 'Преглед на обект')

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
                <a href="{{ route('owner.dashboard') }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                    Back to owner dashboard
                </a>
                <a href="{{ route('owner.entities.edit', $entity) }}" class="rounded border border-gray-400 px-3 py-2 text-sm text-black hover:bg-gray-100">
                    Редакция
                </a>
            </div>
        </div>

        <div class="rounded-lg border border-gray-300">
            <div class="grid gap-0 divide-y divide-gray-200 text-sm">
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3">
                    <div class="font-medium text-gray-700">Име</div>
                    <div class="md:col-span-2 text-black">{{ $entity->name ?: '-' }}</div>
                </div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3">
                    <div class="font-medium text-gray-700">Тип</div>
                    <div class="md:col-span-2 text-black">{{ $entity->entityType?->name ?? '-' }}</div>
                </div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3">
                    <div class="font-medium text-gray-700">Подтип</div>
                    <div class="md:col-span-2 text-black">{{ $entity->entitySubtype?->name ?? '-' }}</div>
                </div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3">
                    <div class="font-medium text-gray-700">Категоризация</div>
                    <div class="md:col-span-2 text-black">{{ $entity->classification ?: '-' }}</div>
                </div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3">
                    <div class="font-medium text-gray-700">Държава</div>
                    <div class="md:col-span-2 text-black">{{ $entity->country?->name ?? '-' }}</div>
                </div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3">
                    <div class="font-medium text-gray-700">Населено място</div>
                    <div class="md:col-span-2 text-black">{{ $entity->place?->name ?? '-' }}</div>
                </div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3">
                    <div class="font-medium text-gray-700">Адрес</div>
                    <div class="md:col-span-2 text-black">{{ $entity->address ?: '-' }}</div>
                </div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3">
                    <div class="font-medium text-gray-700">Телефон</div>
                    <div class="md:col-span-2 text-black">{{ $entity->phone ?: '-' }}</div>
                </div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3">
                    <div class="font-medium text-gray-700">Имейл</div>
                    <div class="md:col-span-2 text-black">{{ $entity->email ?: '-' }}</div>
                </div>
                <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3">
                    <div class="font-medium text-gray-700">Сайт</div>
                    <div class="md:col-span-2 text-black">
                        @if ($entity->website)
                            <a href="{{ $entity->website }}" target="_blank" rel="noopener noreferrer" class="underline">
                                {{ $entity->website }}
                            </a>
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

