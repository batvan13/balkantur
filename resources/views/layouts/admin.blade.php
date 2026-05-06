<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
            crossorigin="anonymous"></script>
</head>
<body class="min-h-screen bg-gray-100 text-black antialiased">
    <div class="min-h-screen">
        <header class="border-b border-gray-300 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3">
                <div>
                    <p class="text-sm text-gray-600">Super Admin Area</p>
                    <h1 class="text-lg font-semibold">@yield('admin_page_title', 'Администрация')</h1>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-700">{{ auth()->user()->name ?: auth()->user()->email }}</span>
                    <a href="{{ route('home') }}" class="rounded border border-gray-400 px-3 py-2 hover:bg-gray-100">Back to home</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded border border-gray-400 px-3 py-2 hover:bg-gray-100">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-4 px-4 py-4 md:grid-cols-[220px_1fr]">
            <aside class="rounded-lg border border-gray-300 bg-white p-3">
                <nav class="space-y-2 text-sm">
                    <a href="{{ route('admin.dashboard') }}" class="block rounded border px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'border-gray-500 bg-gray-100' : 'border-gray-300 hover:bg-gray-100' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.entities.index') }}" class="block rounded border px-3 py-2 {{ request()->routeIs('admin.entities.*') ? 'border-gray-500 bg-gray-100' : 'border-gray-300 hover:bg-gray-100' }}">
                        Обекти
                    </a>
                </nav>
            </aside>

            <main class="space-y-4">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

