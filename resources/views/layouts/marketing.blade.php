<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Platform Kursus Online — Belajar Tanpa Batas')</title>
    <meta name="description" content="@yield('meta_description', 'Platform kursus online untuk belajar keterampilan baru kapan saja, di mana saja — dengan instruktur ahli, kuis interaktif, dan sertifikat penyelesaian.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..800&family=Hanken+Grotesk:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-sans text-ink antialiased" x-data="{ mobileNavOpen: false }" :class="mobileNavOpen && 'overflow-hidden'">

    {{-- ============== Top utility bar ============== --}}
    <div class="hidden bg-brand-950 text-brand-100 lg:block">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-2.5 text-xs">
            <div class="flex items-center gap-5">
                <a href="mailto:halo@platformkursus.id" class="flex items-center gap-1.5 hover:text-white">
                    <svg class="size-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 5h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/><path d="m2.5 5.5 7.5 5 7.5-5"/></svg>
                    halo@platformkursus.id
                </a>
                <span class="flex items-center gap-1.5">
                    <svg class="size-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="10" cy="10" r="7.25"/><path d="M10 5.5V10l3 2"/></svg>
                    Senin–Sabtu, 08.00–20.00 WIB
                </span>
            </div>
            <p class="text-brand-200">Belajar kapan saja, di mana saja — mulai hari ini.</p>
        </div>
    </div>

    {{-- ============== Main nav ============== --}}
    <header class="sticky top-0 z-40 border-b border-black/5 bg-white/90 backdrop-blur-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3.5 sm:px-6 lg:px-8">
            <a href="{{ route('courses.index') }}" class="flex shrink-0 items-center gap-2 font-display text-lg font-bold tracking-tight text-brand-900">
                <span class="flex size-8 items-center justify-center rounded-lg bg-brand-600 text-sm text-white">PK</span>
                Platform Kursus
            </a>

            <nav class="hidden items-center gap-8 text-sm font-medium text-ink-soft lg:flex">
                <a href="{{ route('courses.index') }}" class="transition hover:text-brand-600 {{ request()->routeIs('courses.index') ? 'text-brand-700' : '' }}">Katalog Kursus</a>
                @auth
                    <a href="{{ route('dashboard.index') }}" class="transition hover:text-brand-600">Dasbor Saya</a>
                    <a href="{{ route('quizzes.history') }}" class="transition hover:text-brand-600">Riwayat Kuis</a>
                @endauth
            </nav>

            <div class="flex items-center gap-2.5">
                @auth
                    <div class="hidden items-center gap-2.5 sm:flex">
                        <a href="{{ route('profile.edit') }}" class="rounded-full border border-black/10 px-3 py-1.5 text-sm font-medium text-ink-soft transition hover:border-brand-300 hover:text-brand-700">
                            Hai, {{ Str::of(auth()->user()->name)->before(' ') }}
                        </a>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-black/10 px-3 py-1.5 text-sm font-medium text-ink-soft transition hover:border-brand-300 hover:text-brand-700">Panel Admin</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full px-3 py-1.5 text-sm font-medium text-ink-soft transition hover:text-brand-700">Keluar</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden text-sm font-medium text-ink-soft transition hover:text-brand-700 sm:inline-block">Masuk</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 rounded-full bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-brand-600/20 transition hover:bg-brand-500">
                        Daftar Gratis
                        <svg class="size-3.5" viewBox="0 0 16 14" fill="none"><path d="M7.5 0c0 .7.69 1.74 1.39 2.61.9 1.13 1.97 2.11 3.2 2.86.92.57 2.04 1.11 2.94 1.11M7.5 13.17c0-.7.69-1.74 1.39-2.61.9-1.13 1.97-2.11 3.2-2.86.92-.56 2.04-1.1 2.94-1.1M15.05 6.58H0" stroke="currentColor" stroke-width="1.5"/></svg>
                    </a>
                @endauth

                <button
                    type="button"
                    @click="mobileNavOpen = true"
                    class="inline-flex size-10 items-center justify-center rounded-full text-ink-soft transition hover:bg-black/5 lg:hidden"
                    aria-label="Buka menu"
                >
                    <svg class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M3 6h14M3 10h14M3 14h14"/></svg>
                </button>
            </div>
        </div>
    </header>

    {{-- ============== Mobile nav drawer ============== --}}
    <div
        x-show="mobileNavOpen"
        x-cloak
        class="fixed inset-0 z-50 lg:hidden"
    >
        <div
            x-show="mobileNavOpen"
            x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            @click="mobileNavOpen = false"
            class="absolute inset-0 bg-brand-950/50 backdrop-blur-sm"
        ></div>

        <div
            x-show="mobileNavOpen"
            x-transition:enter="transition ease-out duration-250" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
            class="absolute inset-y-0 right-0 flex w-full max-w-xs flex-col bg-white px-6 py-5 shadow-2xl"
        >
            <div class="flex items-center justify-between">
                <span class="font-display text-lg font-bold text-brand-900">Menu</span>
                <button type="button" @click="mobileNavOpen = false" class="inline-flex size-9 items-center justify-center rounded-full text-ink-soft hover:bg-black/5" aria-label="Tutup menu">
                    <svg class="size-4.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="m4 4 12 12M16 4 4 16"/></svg>
                </button>
            </div>

            <nav class="mt-8 flex flex-col gap-1 text-base font-medium text-ink">
                <a href="{{ route('courses.index') }}" class="rounded-lg px-3 py-2.5 transition hover:bg-brand-50 hover:text-brand-700">Katalog Kursus</a>
                @auth
                    <a href="{{ route('dashboard.index') }}" class="rounded-lg px-3 py-2.5 transition hover:bg-brand-50 hover:text-brand-700">Dasbor Saya</a>
                    <a href="{{ route('profile.edit') }}" class="rounded-lg px-3 py-2.5 transition hover:bg-brand-50 hover:text-brand-700">Profil Saya</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2.5 transition hover:bg-brand-50 hover:text-brand-700">Panel Admin</a>
                    @endif
                @endauth
            </nav>

            <div class="mt-auto flex flex-col gap-3 border-t border-black/5 pt-5">
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-full border border-black/10 px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-black/5">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="w-full rounded-full border border-black/10 px-4 py-2.5 text-center text-sm font-semibold text-ink transition hover:bg-black/5">Masuk</a>
                    <a href="{{ route('register') }}" class="w-full rounded-full bg-brand-600 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-brand-500">Daftar Gratis</a>
                @endauth
            </div>
        </div>
    </div>

    @if (session('notice'))
        <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800">
                <svg class="size-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.36-9.14a.75.75 0 1 0-1.22-.87l-3.236 4.53-1.71-1.71a.75.75 0 0 0-1.06 1.06l2.36 2.36a.75.75 0 0 0 1.14-.094l3.726-5.276Z" clip-rule="evenodd"/></svg>
                {{ session('notice') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    {{-- ============== Footer ============== --}}
    <footer class="mt-24 bg-brand-950 text-brand-200">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-4">
                <div class="sm:col-span-2 lg:col-span-1">
                    <a href="{{ route('courses.index') }}" class="flex items-center gap-2 font-display text-lg font-bold text-white">
                        <span class="flex size-8 items-center justify-center rounded-lg bg-brand-500 text-sm">PK</span>
                        Platform Kursus
                    </a>
                    <p class="mt-4 max-w-xs text-sm leading-relaxed text-brand-300">
                        Belajar keterampilan baru dengan ritme kamu sendiri — materi terstruktur, instruktur berpengalaman, dan kuis untuk menguji pemahamanmu di setiap modul.
                    </p>
                </div>

                <div>
                    <h3 class="font-display text-sm font-semibold text-white">Jelajah</h3>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li><a href="{{ route('courses.index') }}" class="transition hover:text-white">Katalog Kursus</a></li>
                        @auth
                            <li><a href="{{ route('dashboard.index') }}" class="transition hover:text-white">Dasbor Saya</a></li>
                            <li><a href="{{ route('quizzes.history') }}" class="transition hover:text-white">Riwayat Kuis</a></li>
                        @else
                            <li><a href="{{ route('register') }}" class="transition hover:text-white">Daftar Akun</a></li>
                            <li><a href="{{ route('login') }}" class="transition hover:text-white">Masuk</a></li>
                        @endauth
                    </ul>
                </div>

                <div>
                    <h3 class="font-display text-sm font-semibold text-white">Kategori Populer</h3>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        @foreach (\App\Models\Category::orderBy('name')->limit(5)->get() as $footerCategory)
                            <li>
                                <a href="{{ route('courses.index', ['category' => $footerCategory->slug]) }}" class="transition hover:text-white">
                                    {{ $footerCategory->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h3 class="font-display text-sm font-semibold text-white">Hubungi Kami</h3>
                    <ul class="mt-4 space-y-3 text-sm">
                        <li class="flex items-start gap-2.5">
                            <svg class="mt-0.5 size-4 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 5h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/><path d="m2.5 5.5 7.5 5 7.5-5"/></svg>
                            <a href="mailto:halo@platformkursus.id" class="transition hover:text-white">halo@platformkursus.id</a>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <svg class="mt-0.5 size-4 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="10" cy="10" r="7.25"/><path d="M10 5.5V10l3 2"/></svg>
                            Senin–Sabtu, 08.00–20.00 WIB
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-14 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-8 text-xs text-brand-400 sm:flex-row">
                <p>&copy; {{ date('Y') }} Platform Kursus Online. Semua hak dilindungi.</p>
                <p>Dibangun dengan Laravel &amp; Tailwind CSS.</p>
            </div>
        </div>
    </footer>
</body>
</html>
