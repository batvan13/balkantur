@extends('layouts.app')

@section('title', __('Register'))

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight">{{ __('Register') }}</h1>
    <p class="mt-2 text-sm text-slate-600"><a href="{{ route('login') }}" class="text-indigo-600 hover:underline">{{ __('Login') }}</a></p>

    <form method="post" action="{{ route('register') }}" class="mt-8 space-y-4 rounded-lg bg-white p-6 shadow-sm">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">{{ __('Name') }}</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                   class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">{{ __('Email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email"
                   class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700">{{ __('Password') }}</label>
            <input type="password" name="password" id="password" required autocomplete="new-password"
                   class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">{{ __('Confirm Password') }}</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                   class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>
        <button type="submit"
                class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            {{ __('Register') }}
        </button>
    </form>
@endsection
