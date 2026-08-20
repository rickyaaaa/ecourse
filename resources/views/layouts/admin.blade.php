<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin — Platform Kursus Online')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body
    class="min-h-screen bg-admin-muted font-admin text-admin-foreground antialiased"
    x-data="{ sidebarOpen: false }"
>
    {{-- Overlay mobile --}}
    <div
        x-show="sidebarOpen"
        x-cloak
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/50 lg:hidden"
    ></div>

    <div class="flex min-h-screen">
        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-[280px] shrink-0 -translate-x-full transform flex-col overflow-hidden border-r border-admin-border bg-white transition-transform duration-300 lg:translate-x-0"
            :class="sidebarOpen && '!translate-x-0'"
        >
            <div class="flex h-[90px] shrink-0 items-center justify-between gap-3 border-b border-admin-border px-5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <span class="flex h-9 w-11 shrink-0 items-center justify-center rounded-xl bg-admin-primary">
                        <i data-lucide="graduation-cap" class="h-5 w-5 text-white"></i>
                    </span>
                    <span class="text-lg font-semibold">Platform Kursus</span>
                </a>
                <button
                    @click="sidebarOpen = false"
                    aria-label="Tutup menu"
                    class="flex size-10 shrink-0 items-center justify-center rounded-xl ring-1 ring-admin-border hover:ring-admin-primary transition-all lg:hidden"
                >
                    <i data-lucide="x" class="size-5 text-admin-secondary"></i>
                </button>
            </div>

            <nav class="flex flex-1 flex-col gap-6 overflow-y-auto p-5 pb-6">
                <div class="flex flex-col gap-3">
                    <h3 class="text-sm font-medium text-admin-secondary">Menu Utama</h3>
                    <div class="flex flex-col gap-1">
                        @php
                            $adminNavItems = [
                                ['route' => 'admin.dashboard', 'label' => 'Dasbor', 'icon' => 'layout-dashboard'],
                                ['route' => 'admin.courses.index', 'label' => 'Kelola Kursus', 'icon' => 'book-open'],
                                ['route' => 'admin.modules.index', 'label' => 'Kelola Materi', 'icon' => 'layers'],
                                ['route' => 'admin.quizzes.index', 'label' => 'Kelola Kuis', 'icon' => 'clipboard-list'],
                                ['route' => 'admin.participants.index', 'label' => 'Kelola Peserta', 'icon' => 'users'],
                            ];
                        @endphp

                        @foreach ($adminNavItems as $item)
                            @php $isActive = request()->routeIs($item['route']); @endphp
                            <a
                                href="{{ route($item['route']) }}"
                                class="flex items-center gap-3 rounded-xl p-3.5 text-sm font-medium transition-all {{ $isActive ? 'bg-admin-muted font-semibold text-admin-foreground' : 'text-admin-secondary hover:bg-admin-muted hover:text-admin-foreground' }}"
                            >
                                <i data-lucide="{{ $item['icon'] }}" class="size-5 shrink-0"></i>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="mt-auto flex flex-col gap-1 border-t border-admin-border pt-5">
                    <a href="{{ route('courses.index') }}" class="flex items-center gap-3 rounded-xl p-3.5 text-sm font-medium text-admin-secondary hover:bg-admin-muted hover:text-admin-foreground transition-all">
                        <i data-lucide="arrow-left" class="size-5 shrink-0"></i>
                        Kembali ke Situs
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-xl p-3.5 text-left text-sm font-medium text-admin-secondary hover:bg-admin-error-light hover:text-admin-error-dark transition-all">
                            <i data-lucide="log-out" class="size-5 shrink-0"></i>
                            Keluar
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col lg:ml-[280px]">
            <header class="flex h-[90px] shrink-0 items-center justify-between gap-3 border-b border-admin-border bg-white px-4 sm:px-6">
                <button
                    @click="sidebarOpen = true"
                    aria-label="Buka menu"
                    class="flex size-11 items-center justify-center rounded-xl ring-1 ring-admin-border hover:ring-admin-primary transition-all lg:hidden"
                >
                    <i data-lucide="menu" class="size-6 text-admin-foreground"></i>
                </button>

                <h1 class="text-lg font-bold text-admin-foreground sm:text-xl">@yield('page-title', 'Panel Admin')</h1>

                <div class="flex items-center gap-3 border-l border-admin-border pl-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-admin-primary text-sm font-semibold text-white">
                        {{ Str::of(auth()->user()->name)->substr(0, 1)->upper() }}
                    </span>
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-semibold text-admin-foreground">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-admin-secondary">Admin</p>
                    </div>
                </div>
            </header>

            @if (session('notice'))
                <div class="mx-4 mt-4 flex items-center gap-2 rounded-xl border border-admin-success/30 bg-admin-success-light px-4 py-3 text-sm text-admin-success-dark sm:mx-6">
                    <i data-lucide="check-circle-2" class="size-4 shrink-0"></i>
                    {{ session('notice') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 mt-4 flex items-center gap-2 rounded-xl border border-admin-error/30 bg-admin-error-light px-4 py-3 text-sm text-admin-error-dark sm:mx-6">
                    <i data-lucide="alert-circle" class="size-4 shrink-0"></i>
                    {{ session('error') }}
                </div>
            @endif

            <main class="flex-1 bg-admin-muted px-4 py-6 sm:px-6 sm:py-8">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
