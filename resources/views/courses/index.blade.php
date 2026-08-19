@extends('layouts.marketing')

@section('title', 'Platform Kursus Online — Belajar Tanpa Batas')

@section('content')
{{-- ============== Hero ============== --}}
<section class="relative overflow-hidden bg-brand-950">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -left-32 top-0 size-[32rem] rounded-full bg-brand-600/25 blur-3xl"></div>
        <div class="absolute -right-20 top-1/3 size-96 rounded-full bg-gold-500/15 blur-3xl"></div>
    </div>

    <div class="relative mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:gap-8 lg:px-8 lg:py-24">
        <div class="reveal">
            <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-1.5 text-xs font-medium text-brand-100">
                <span class="size-1.5 rounded-full bg-gold-400"></span>
                {{ number_format($stats['courses'], 0, ',', '.') }} kursus siap diikuti hari ini
            </span>

            <h1 class="mt-6 text-balance font-display text-4xl font-bold leading-[1.08] tracking-[-0.03em] text-white sm:text-5xl lg:text-[3.4rem]">
                Belajar keterampilan baru, dengan ritme kamu sendiri.
            </h1>

            <p class="mt-5 max-w-lg text-pretty text-lg leading-relaxed text-brand-200">
                Materi terstruktur, kuis di tiap modul, dan progres yang selalu tersimpan — mulai kapan saja, lanjutkan kapan pun kamu sempat.
            </p>

            <form method="GET" action="{{ route('courses.index') }}" class="mt-8 flex max-w-lg flex-col gap-2.5 sm:flex-row">
                <label class="relative flex-1">
                    <span class="sr-only">Cari kursus</span>
                    <svg class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-ink-soft" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="9" cy="9" r="6.25"/><path d="m17 17-3.5-3.5" stroke-linecap="round"/></svg>
                    <input
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari kursus, mis. &ldquo;Laravel&rdquo;, &ldquo;desain&rdquo;..."
                        class="w-full rounded-full border-0 bg-white py-3.5 pl-11 pr-4 text-sm text-ink shadow-lg shadow-black/10 placeholder:text-ink-soft focus:outline-none focus:ring-2 focus:ring-gold-400"
                    >
                </label>
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-full bg-gold-500 px-6 py-3.5 text-sm font-semibold text-brand-950 transition hover:bg-gold-400">
                    Cari Kursus
                </button>
            </form>

            <dl class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-4 border-t border-white/10 pt-8">
                <div>
                    <dt class="text-xs text-brand-300">Kursus Aktif</dt>
                    <dd class="font-display text-2xl font-bold text-white">{{ number_format($stats['courses'], 0, ',', '.') }}+</dd>
                </div>
                <div>
                    <dt class="text-xs text-brand-300">Kategori</dt>
                    <dd class="font-display text-2xl font-bold text-white">{{ $stats['categories'] }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-brand-300">Pelajar Terdaftar</dt>
                    <dd class="font-display text-2xl font-bold text-white">{{ number_format($stats['students'], 0, ',', '.') }}+</dd>
                </div>
            </dl>
        </div>

        <div class="reveal delay-2 relative">
            <div class="relative overflow-hidden rounded-3xl shadow-2xl shadow-black/40">
                <img
                    src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80"
                    alt="Sekelompok pelajar belajar bersama sambil tertawa di sekitar meja"
                    class="aspect-[4/5] w-full object-cover sm:aspect-[5/4] lg:aspect-[4/5]"
                    loading="eager"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-brand-950/50 via-transparent to-transparent"></div>
            </div>

            <div class="absolute -bottom-6 -left-4 w-52 rounded-2xl bg-white p-4 shadow-xl sm:-left-8 sm:w-60">
                <div class="flex items-center gap-3">
                    <div class="flex -space-x-2">
                        @foreach (['bg-brand-400', 'bg-gold-400', 'bg-brand-600'] as $avatarColor)
                            <span class="flex size-8 items-center justify-center rounded-full {{ $avatarColor }} text-xs font-semibold text-white ring-2 ring-white">
                                {{ ['AL', 'BW', 'CR'][$loop->index] }}
                            </span>
                        @endforeach
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-ink">Bergabung minggu ini</p>
                        <div class="mt-0.5 flex items-center gap-0.5 text-gold-500">
                            @for ($i = 0; $i < 5; $i++)
                                <svg class="size-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10 1.5l2.6 5.6 6.1.6-4.6 4.1 1.3 6-5.4-3.1-5.4 3.1 1.3-6-4.6-4.1 6.1-.6z"/></svg>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============== Value proposition ============== --}}
<section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-16">
        <div class="reveal order-2 lg:order-1">
            <div class="relative">
                <img
                    src="https://images.unsplash.com/photo-1758270705290-62b6294dd044?auto=format&fit=crop&w=1200&q=80"
                    alt="Sekelompok pelajar berdiskusi di sekitar laptop di ruang kelas"
                    class="aspect-[4/3] w-full rounded-3xl object-cover shadow-lg"
                    loading="lazy"
                >
                <div class="absolute -bottom-6 -right-6 hidden rounded-2xl bg-brand-600 px-5 py-4 text-white shadow-xl sm:block">
                    <p class="font-display text-3xl font-bold">{{ $stats['categories'] }}</p>
                    <p class="text-xs text-brand-100">Kategori kursus</p>
                </div>
            </div>
        </div>

        <div class="reveal delay-1 order-1 lg:order-2">
            <span class="text-sm font-semibold text-brand-600">Kenapa Platform Kursus</span>
            <h2 class="mt-2 text-balance font-display text-3xl font-bold tracking-[-0.02em] text-ink sm:text-4xl">
                Dibangun supaya kamu benar-benar menyelesaikan apa yang kamu mulai.
            </h2>
            <p class="mt-4 text-pretty leading-relaxed text-ink-soft">
                Bukan cuma kumpulan video — setiap kursus disusun jadi modul dan pelajaran yang berurutan, dilengkapi kuis untuk menguji pemahamanmu, dan progresmu selalu tersimpan agar mudah dilanjutkan.
            </p>

            <ul class="mt-8 space-y-5">
                <li class="flex gap-4">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700">
                        <svg class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 6h12M4 10h12M4 14h8" stroke-linecap="round"/></svg>
                    </span>
                    <div>
                        <h3 class="font-semibold text-ink">Silabus terstruktur</h3>
                        <p class="mt-0.5 text-sm leading-relaxed text-ink-soft">Modul demi modul, pelajaran teks maupun video, disusun berurutan agar mudah diikuti.</p>
                    </div>
                </li>
                <li class="flex gap-4">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700">
                        <svg class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M10 2 3 6v4c0 4.4 3 7.4 7 8.5 4-1.1 7-4.1 7-8.5V6l-7-4Z"/><path d="m7.3 10 1.9 1.9L13 8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <div>
                        <h3 class="font-semibold text-ink">Kuis di tiap modul</h3>
                        <p class="mt-0.5 text-sm leading-relaxed text-ink-soft">Uji pemahamanmu sebelum lanjut ke modul berikutnya, lengkap dengan pembahasan jawaban.</p>
                    </div>
                </li>
                <li class="flex gap-4">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700">
                        <svg class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M10 3v14M4 10h12" stroke-linecap="round"/></svg>
                    </span>
                    <div>
                        <h3 class="font-semibold text-ink">Progres tersimpan otomatis</h3>
                        <p class="mt-0.5 text-sm leading-relaxed text-ink-soft">Berhenti di tengah pelajaran? Dasbormu selalu tahu ke mana kamu harus lanjut.</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</section>

{{-- ============== Katalog kursus ============== --}}
<section id="katalog" class="bg-surface py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="reveal flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <span class="text-sm font-semibold text-brand-600">Katalog Kursus</span>
                <h2 class="mt-2 text-balance font-display text-3xl font-bold tracking-[-0.02em] text-ink sm:text-4xl">
                    Pilih kursus, mulai belajar hari ini.
                </h2>
            </div>
        </div>

        <form
            method="GET"
            action="{{ route('courses.index') }}"
            class="reveal delay-1 mt-8 flex flex-col gap-3 rounded-2xl border border-black/5 bg-white p-3 shadow-sm sm:flex-row sm:items-center"
        >
            <label class="relative flex-1">
                <span class="sr-only">Cari kursus</span>
                <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-ink-soft" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="9" cy="9" r="6.25"/><path d="m17 17-3.5-3.5" stroke-linecap="round"/></svg>
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    x-data
                    x-on:input.debounce.400ms="$el.form.requestSubmit()"
                    placeholder="Cari kursus berdasarkan judul atau deskripsi..."
                    class="w-full rounded-xl border border-black/10 py-2.5 pl-10 pr-4 text-sm text-ink placeholder:text-ink-soft focus:border-brand-400 focus:outline-none focus:ring-1 focus:ring-brand-400"
                >
            </label>

            <label class="sm:w-56">
                <span class="sr-only">Filter kategori</span>
                <select
                    name="category"
                    x-data
                    x-on:change="$el.form.requestSubmit()"
                    class="w-full rounded-xl border border-black/10 px-4 py-2.5 text-sm text-ink focus:border-brand-400 focus:outline-none focus:ring-1 focus:ring-brand-400"
                >
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <noscript>
                <button type="submit" class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-500">
                    Cari
                </button>
            </noscript>
        </form>

        <p class="mt-6 text-sm text-ink-soft">
            Menampilkan {{ $courses->count() }} dari {{ $courses->total() }} kursus
            @if ($search !== '' || $selectedCategory !== '')
                (difilter)
            @endif
        </p>

        <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($courses as $course)
                <div class="reveal delay-{{ min(4, ($loop->index % 3) + 1) }}">
                    <x-course-card :course="$course" />
                </div>
            @endforeach
        </div>

        @if ($courses->isEmpty())
            <div class="mt-4 rounded-2xl border border-dashed border-black/10 p-12 text-center text-ink-soft">
                @if ($search !== '' || $selectedCategory !== '')
                    Tidak ada kursus yang cocok dengan pencarian atau filter kamu.
                @else
                    Belum ada kursus yang tersedia.
                @endif
            </div>
        @endif

        @if ($courses->hasPages())
            <div class="mt-10">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</section>

{{-- ============== Kenapa memilih kami ============== --}}
<section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-16">
        <div class="reveal relative">
            <img
                src="https://images.unsplash.com/photo-1597154036737-bc0dea584279?auto=format&fit=crop&w=1200&q=80"
                alt="Instruktur merekam materi pelajaran di studio rumahan"
                class="aspect-[5/4] w-full rounded-3xl object-cover shadow-lg"
                loading="lazy"
            >
        </div>

        <div class="reveal delay-1">
            <span class="text-sm font-semibold text-brand-600">Kenapa Belajar di Sini</span>
            <h2 class="mt-2 text-balance font-display text-3xl font-bold tracking-[-0.02em] text-ink sm:text-4xl">
                Empat alasan pelajar kami bertahan sampai lulus.
            </h2>
            <p class="mt-4 leading-relaxed text-ink-soft">
                Kami merancang setiap detail — dari cara materi disusun sampai bagaimana progresmu ditampilkan — supaya belajar online tidak terasa sepi atau membingungkan.
            </p>

            <div class="mt-8 divide-y divide-black/5 border-t border-black/5">
                <div class="flex items-start gap-4 py-5">
                    <svg class="mt-0.5 size-5 shrink-0 text-brand-500" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M10 2 2 6l8 4 8-4-8-4Z"/><path d="M4 8.4V13c0 1.4 2.7 3 6 3s6-1.6 6-3V8.4" stroke-linecap="round"/></svg>
                    <div>
                        <h3 class="font-display font-semibold text-ink">Instruktur Berpengalaman</h3>
                        <p class="mt-1 text-sm leading-relaxed text-ink-soft">Materi disusun praktisi yang paham bagaimana pemula belajar paling efektif.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 py-5">
                    <svg class="mt-0.5 size-5 shrink-0 text-brand-500" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="10" cy="10" r="7.25"/><path d="M10 5.5V10l3 2" stroke-linecap="round"/></svg>
                    <div>
                        <h3 class="font-display font-semibold text-ink">Belajar Sesuai Ritme</h3>
                        <p class="mt-1 text-sm leading-relaxed text-ink-soft">Tidak ada jadwal kaku — akses materi kapan pun kamu sempat, progres tetap tersimpan.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 py-5">
                    <svg class="mt-0.5 size-5 shrink-0 text-brand-500" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 6h12M4 10h8M4 14h5" stroke-linecap="round"/></svg>
                    <div>
                        <h3 class="font-display font-semibold text-ink">Kuis & Pembahasan</h3>
                        <p class="mt-1 text-sm leading-relaxed text-ink-soft">Setiap modul ditutup kuis singkat, lengkap dengan pembahasan tiap jawaban.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 py-5">
                    <svg class="mt-0.5 size-5 shrink-0 text-brand-500" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 10.5 9 13.5 14.5 7" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="10" r="7.25"/></svg>
                    <div>
                        <h3 class="font-display font-semibold text-ink">Progres yang Jelas</h3>
                        <p class="mt-1 text-sm leading-relaxed text-ink-soft">Dasbor menunjukkan persis modul mana yang sudah selesai dan mana yang lanjut.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============== Testimoni ============== --}}
<section class="overflow-hidden bg-brand-950 py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="reveal text-center">
            <span class="text-sm font-semibold text-gold-400">Testimoni</span>
            <h2 class="mt-2 text-balance font-display text-3xl font-bold tracking-[-0.02em] text-white sm:text-4xl">
                Kata mereka yang sudah belajar bersama kami.
            </h2>
        </div>

        @php
            $testimonials = [
                ['name' => 'Ayu Lestari', 'role' => 'Pelajar Pengembangan Web', 'initials' => 'AL', 'color' => 'bg-brand-500', 'quote' => 'Silabusnya runtut banget — dari yang paling dasar sampai bisa bikin proyek sendiri. Kuis di tiap modul bikin saya yakin materinya benar-benar nempel.'],
                ['name' => 'Bagas Wicaksono', 'role' => 'Pelajar Data & AI', 'initials' => 'BW', 'color' => 'bg-gold-500', 'quote' => 'Suka karena bisa belajar sambil kerja. Progresnya kesimpen otomatis, jadi kapan pun buka lagi langsung lanjut dari pelajaran terakhir.'],
                ['name' => 'Citra Ramadhani', 'role' => 'Pelajar Desain', 'initials' => 'CR', 'color' => 'bg-brand-400', 'quote' => 'Pembahasan di setiap soal kuis benar-benar membantu — bukan cuma dikasih tahu salah, tapi juga kenapa jawabannya salah.'],
                ['name' => 'Fajar Nugroho', 'role' => 'Pelajar Bisnis & Karier', 'initials' => 'FN', 'color' => 'bg-gold-600', 'quote' => 'Awalnya ragu belajar online efektif atau tidak, ternyata terstrukturnya justru lebih rapi dibanding kelas biasa yang saya ikuti sebelumnya.'],
            ];
        @endphp

        <div class="reveal delay-1 mt-12 flex snap-x snap-mandatory gap-5 overflow-x-auto scrollbar-hidden pb-4">
            @foreach ($testimonials as $testimonial)
                <figure class="w-[19rem] shrink-0 snap-start rounded-2xl bg-white/5 p-6 ring-1 ring-white/10 sm:w-[22rem]">
                    <div class="flex items-center gap-0.5 text-gold-400">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 1.5l2.6 5.6 6.1.6-4.6 4.1 1.3 6-5.4-3.1-5.4 3.1 1.3-6-4.6-4.1 6.1-.6z"/></svg>
                        @endfor
                    </div>
                    <blockquote class="mt-4 text-pretty text-sm leading-relaxed text-brand-100">
                        &ldquo;{{ $testimonial['quote'] }}&rdquo;
                    </blockquote>
                    <figcaption class="mt-5 flex items-center gap-3 border-t border-white/10 pt-4">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-full {{ $testimonial['color'] }} text-xs font-semibold text-white">
                            {{ $testimonial['initials'] }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-white">{{ $testimonial['name'] }}</p>
                            <p class="text-xs text-brand-300">{{ $testimonial['role'] }}</p>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>

{{-- ============== CTA ============== --}}
<section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
    <div class="reveal relative overflow-hidden rounded-3xl bg-brand-600 px-8 py-14 sm:px-16">
        <div class="pointer-events-none absolute -right-16 -top-16 size-64 rounded-full bg-white/10 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-20 left-1/3 size-72 rounded-full bg-gold-400/20 blur-3xl"></div>

        <div class="relative mx-auto max-w-xl text-center">
            <h2 class="text-balance font-display text-3xl font-bold tracking-[-0.02em] text-white sm:text-4xl">
                Siap mulai belajar sesuatu yang baru?
            </h2>
            <p class="mt-4 text-pretty text-brand-100">
                Daftar gratis, jelajahi katalog kursus, dan mulai modul pertamamu hari ini juga.
            </p>
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                @auth
                    <a href="#katalog" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-brand-700 transition hover:bg-brand-50">
                        Jelajahi Katalog Kursus
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-gold-500 px-6 py-3 text-sm font-semibold text-brand-950 transition hover:bg-gold-400">
                        Daftar Gratis Sekarang
                    </a>
                    <a href="#katalog" class="inline-flex items-center justify-center rounded-full border border-white/30 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Lihat Katalog Kursus
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>
@endsection
