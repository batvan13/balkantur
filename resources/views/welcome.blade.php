<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Balkantur</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 text-black antialiased">
    <header class="border-b border-gray-300 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
            <div class="text-lg font-semibold">Balkantur</div>
            <nav class="flex items-center gap-2 text-sm">
                @guest
                    <a href="{{ route('login') }}" class="rounded border border-gray-400 px-3 py-2 hover:bg-gray-100">Вход</a>
                    <a href="{{ route('register') }}" class="rounded border border-gray-400 px-3 py-2 hover:bg-gray-100">Регистрация</a>
                @endguest

                @auth
                    @if (auth()->user()->hasAnyRole([\App\Models\User::ROLE_OWNER, \App\Models\User::ROLE_SUPER_ADMIN]))
                        <a href="{{ route('owner.dashboard') }}" class="rounded border border-gray-400 px-3 py-2 hover:bg-gray-100">Работен панел</a>
                    @endif
                    @if (auth()->user()->hasRole(\App\Models\User::ROLE_SUPER_ADMIN))
                        <a href="{{ route('admin.dashboard') }}" class="rounded border border-gray-400 px-3 py-2 hover:bg-gray-100">Админ панел</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded border border-gray-400 px-3 py-2 hover:bg-gray-100">Изход</button>
                    </form>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl space-y-8 px-4 py-10">
        <section class="rounded-lg border border-gray-300 bg-white p-6">
            <h1 class="text-3xl font-semibold">Туристическа платформа за България и Балканите</h1>
            <p class="mt-3 text-sm text-gray-700">Откривайте, добавяйте и управлявайте туристически обекти на едно място.</p>
            <div class="mt-5 flex flex-wrap gap-2">
                @auth
                    <a href="{{ route('owner.dashboard') }}" class="rounded border border-gray-500 bg-gray-900 px-4 py-2 text-sm text-white hover:bg-black">Добави обект</a>
                @else
                    <a href="{{ route('register') }}" class="rounded border border-gray-500 bg-gray-900 px-4 py-2 text-sm text-white hover:bg-black">Добави обект</a>
                    <a href="{{ route('login') }}" class="rounded border border-gray-400 px-4 py-2 text-sm hover:bg-gray-100">Вход</a>
                @endauth
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-gray-300 bg-white p-4">
                <h2 class="font-semibold">Места за настаняване</h2>
                <p class="mt-2 text-sm text-gray-700">Хотели, къщи за гости, апартаменти и вили.</p>
            </div>
            <div class="rounded-lg border border-gray-300 bg-white p-4">
                <h2 class="font-semibold">Места за хранене</h2>
                <p class="mt-2 text-sm text-gray-700">Ресторанти, механи, бистра и барове.</p>
            </div>
            <div class="rounded-lg border border-gray-300 bg-white p-4">
                <h2 class="font-semibold">Атракции</h2>
                <p class="mt-2 text-sm text-gray-700">Музеи, крепости, плажове и водопади.</p>
            </div>
        </section>
    </main>
</body>
</html>
