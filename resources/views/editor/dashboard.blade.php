@extends('layouts.app')

@section('title', __('Editor'))

@section('content')
    <div class="rounded-lg bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold">{{ __('Editor area') }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ __('Route group: roles super_admin or editor.') }}</p>
        <p class="mt-4"><a href="{{ route('home') }}" class="text-sm text-indigo-600 hover:underline">{{ __('Back to home') }}</a></p>
    </div>
@endsection
