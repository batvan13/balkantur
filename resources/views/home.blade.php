@extends('layouts.app')

@section('title', __('Home'))

@section('content')
    <div class="rounded-lg bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold">{{ __('Home') }}</h1>
        <p class="mt-2 text-sm text-slate-600">
            {{ __('You are signed in as :email (role: :role).', ['email' => auth()->user()->email, 'role' => auth()->user()->role]) }}
        </p>
        <ul class="mt-4 space-y-2 text-sm text-indigo-700">
            @if (auth()->user()->hasAnyRole([\App\Models\User::ROLE_SUPER_ADMIN]))
                <li><a href="{{ route('admin.dashboard') }}" class="hover:underline">{{ __('Super admin area') }}</a></li>
            @endif
            @if (auth()->user()->hasAnyRole([\App\Models\User::ROLE_SUPER_ADMIN, \App\Models\User::ROLE_EDITOR]))
                <li><a href="{{ route('editor.dashboard') }}" class="hover:underline">{{ __('Editor area') }}</a></li>
            @endif
            @if (auth()->user()->hasAnyRole([\App\Models\User::ROLE_SUPER_ADMIN, \App\Models\User::ROLE_OWNER]))
                <li><a href="{{ route('owner.dashboard') }}" class="hover:underline">{{ __('Owner area') }}</a></li>
            @endif
        </ul>
        <form method="post" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button type="submit" class="text-sm font-medium text-slate-600 hover:text-slate-900">{{ __('Log out') }}</button>
        </form>
    </div>
@endsection
