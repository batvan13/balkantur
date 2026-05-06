@extends('layouts.app')

@section('title', __('Login'))

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight">{{ __('Login') }}</h1>
    <p class="mt-2 text-sm text-slate-600"><a href="{{ route('register') }}" class="text-indigo-600 hover:underline">{{ __('Register') }}</a></p>

    <form method="post" action="{{ route('login') }}" class="mt-8 space-y-4 rounded-lg bg-white p-6 shadow-sm">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">{{ __('Email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                   class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700">{{ __('Password') }}</label>
            <input type="password" name="password" id="password" required autocomplete="current-password"
                   class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="remember" id="remember" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
            <label for="remember" class="text-sm text-slate-700">{{ __('Remember me') }}</label>
        </div>
        <button type="submit"
                class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            {{ __('Login') }}
        </button>
    </form>
@endsection
