<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Platform Kursus Online')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..800&family=Hanken+Grotesk:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-sans text-ink antialiased">
    <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
        {{-- Panel bermerek — disembunyikan di layar kecil supaya form jadi fokus utama --}}
        <div class="relative hidden overflow-hidden bg-brand-950 lg:block">
            <img
                src="https://images.unsplash.com/photo-1769794370990-614f765fa360?auto=format&fit=crop&w=1200&q=80"
                alt="Meja belajar hangat dengan lilin, buku catatan, dan laptop"
                class="absolute inset-0 size-full object-cover opacity-40"
                loading="eager"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-brand-950 via-brand-950/70 to-brand-950/20"></div>
            <div class="pointer-events-none absolute -left-24 top-10 size-72 rounded-full bg-brand-500/30 blur-3xl"></div>

            <div class="relative flex h-full flex-col justify-between p-12">
                <a href="{{ route('courses.index') }}" class="flex items-center gap-2 font-display text-lg font-bold text-white">
                    <span class="flex size-8 items-center justify-center rounded-lg bg-brand-500 text-sm">PK</span>
                    Platform Kursus
                </a>

                <blockquote class="max-w-md">
                    <svg class="size-8 text-gold-400" viewBox="0 0 32 24" fill="currentColor"><path d="M0 24V14.4C0 6.4 4.8 1.2 12.8 0l1.6 4C9.2 5.2 6.8 8 6.8 12h6.8v12H0Zm18 0V14.4c0-8 4.8-13.2 12.8-14.4L32 4c-5.2 1.2-7.6 4-7.6 8h6.8v12H18Z"/></svg>
                    <p class="mt-4 text-balance font-display text-2xl font-medium leading-snug text-white">
                        Materinya runtut banget — dari dasar sampai bisa bikin proyek sendiri.
                    </p>
                    <footer class="mt-5 flex items-center gap-3">
                        <span class="flex size-10 items-center justify-center rounded-full bg-brand-500 text-sm font-semibold text-white">AL</span>
                        <div>
                            <p class="text-sm font-semibold text-white">Ayu Lestari</p>
                            <p class="text-xs text-brand-300">Pelajar Pengembangan Web</p>
                        </div>
                    </footer>
                </blockquote>

                <p class="text-xs text-brand-400">&copy; {{ date('Y') }} Platform Kursus Online</p>
            </div>
        </div>

        {{-- Panel form --}}
        <div class="flex flex-col px-4 py-8 sm:px-6 lg:px-16 lg:py-12">
            <a href="{{ route('courses.index') }}" class="flex items-center gap-2 font-display text-lg font-bold text-brand-900 lg:hidden">
                <span class="flex size-8 items-center justify-center rounded-lg bg-brand-600 text-sm text-white">PK</span>
                Platform Kursus
            </a>

            @if (session('notice'))
                <div class="mt-6 flex items-center gap-2 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800">
                    <svg class="size-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.36-9.14a.75.75 0 1 0-1.22-.87l-3.236 4.53-1.71-1.71a.75.75 0 0 0-1.06 1.06l2.36 2.36a.75.75 0 0 0 1.14-.094l3.726-5.276Z" clip-rule="evenodd"/></svg>
                    {{ session('notice') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
            @endif

            <main class="flex flex-1 items-center py-8 lg:py-0">
                <div class="mx-auto w-full max-w-sm">
                    @yield('content')
                </div>
            </main>

            <footer class="text-center text-xs text-ink-soft lg:text-left">
                &copy; {{ date('Y') }} Platform Kursus Online. Semua hak dilindungi.
            </footer>
        </div>
    </div>
</body>
</html>
