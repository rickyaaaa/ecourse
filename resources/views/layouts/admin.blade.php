<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin — Platform Kursus Online')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="min-h-screen bg-gray-50 text-gray-900 antialiased"
    x-data="{ sidebarOpen: false }"
>
    <div class="flex min-h-screen">
        {{-- Overlay mobile, klik untuk menutup sidebar --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
        ></div>

        <aside
            class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full transform border-r border-gray-200 bg-white transition-transform duration-200 ease-in-out lg:static lg:translate-x-0"
            :class="sidebarOpen && '!translate-x-0'"
        >
            <div class="flex h-16 items-center justify-between border-b border-gray-200 px-6">
                <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold text-indigo-600">
                    Panel Admin
                </a>
                <button @click="sidebarOpen = false" class="text-gray-400 hover:text-gray-600 lg:hidden" aria-label="Tutup menu">
                    ✕
                </button>
            </div>

            <nav class="space-y-1 px-3 py-4 text-sm font-medium">
                @php
                    $adminNavItems = [
                        ['route' => 'admin.dashboard', 'label' => 'Dasbor', 'icon' => '📊'],
                        ['route' => 'admin.courses.index', 'label' => 'Kelola Kursus', 'icon' => '📚'],
                        ['route' => 'admin.modules.index', 'label' => 'Kelola Materi', 'icon' => '🧩'],
                        ['route' => 'admin.quizzes.index', 'label' => 'Kelola Kuis', 'icon' => '📝'],
                        ['route' => 'admin.participants.index', 'label' => 'Kelola Peserta', 'icon' => '👥'],
                    ];
                @endphp

                @foreach ($adminNavItems as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="flex items-center gap-3 rounded-md px-3 py-2 {{ request()->routeIs($item['route']) ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <span aria-hidden="true">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="absolute inset-x-0 bottom-0 border-t border-gray-200 p-3">
                <a href="{{ route('courses.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    <span aria-hidden="true">↩️</span>
                    Kembali ke Situs
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-left text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                        <span aria-hidden="true">🚪</span>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 sm:px-6">
                <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-700 lg:hidden" aria-label="Buka menu">
                    ☰
                </button>
                <h1 class="text-base font-semibold text-gray-900">@yield('page-title', 'Panel Admin')</h1>
                <span class="hidden text-sm text-gray-500 sm:inline">Hai, {{ auth()->user()->name }}</span>
            </header>

            @if (session('notice'))
                <div class="mx-4 mt-4 rounded-md border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800 sm:mx-6">
                    {{ session('notice') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 sm:mx-6">
                    {{ session('error') }}
                </div>
            @endif

            <main class="flex-1 px-4 py-6 sm:px-6 sm:py-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
