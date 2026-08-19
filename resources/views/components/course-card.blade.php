@props(['course'])

@php
    $isPopular = ($course['students_count'] ?? 0) > 1000;
@endphp

<article
    {{ $attributes->class(['group relative flex flex-col overflow-hidden rounded-2xl border border-black/5 bg-white shadow-[0_1px_2px_rgba(16,15,40,0.04)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_40px_-16px_rgba(30,20,90,0.18)]']) }}
>
    <div class="relative flex h-40 items-center justify-center overflow-hidden bg-gradient-to-br {{ $course['thumbnail_color'] }}">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.25),transparent_55%)]"></div>
        <span class="relative text-5xl drop-shadow-sm" aria-hidden="true">{{ $course['thumbnail_icon'] }}</span>

        @if ($isPopular)
            <span class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full bg-white/95 px-2.5 py-1 text-xs font-semibold text-ink shadow-sm">
                🔥 Populer
            </span>
        @endif

        <x-enroll-status :enrollment="$course['enrollment'] ?? null" variant="badge" />
    </div>

    <div class="flex flex-1 flex-col gap-3 p-5">
        <div class="flex items-center justify-between gap-2">
            <span class="rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-700">
                {{ $course['category'] }}
            </span>
            @if (! empty($course['level']))
                <span class="rounded-full bg-black/5 px-2.5 py-1 text-xs font-medium text-ink-soft">
                    {{ $course['level'] }}
                </span>
            @endif
        </div>

        <h2 class="line-clamp-2 font-display text-base font-semibold leading-snug text-ink transition group-hover:text-brand-700">
            {{ $course['title'] }}
        </h2>

        <p class="line-clamp-2 flex-1 text-sm leading-relaxed text-ink-soft">
            {{ $course['description'] }}
        </p>

        <dl class="flex flex-wrap items-center gap-x-4 gap-y-1 border-t border-black/5 pt-3 text-xs text-ink-soft">
            <div class="flex items-center gap-1" title="Jumlah modul">
                <dt class="sr-only">Modul</dt>
                <dd>📚 {{ $course['modules_count'] }} modul</dd>
            </div>
            <div class="flex items-center gap-1" title="Jumlah pelajaran">
                <dt class="sr-only">Pelajaran</dt>
                <dd>📝 {{ $course['lessons_count'] }} pelajaran</dd>
            </div>
            <div class="flex items-center gap-1" title="Jumlah pelajar terdaftar">
                <dt class="sr-only">Pelajar</dt>
                <dd>👥 {{ number_format($course['students_count'], 0, ',', '.') }} pelajar</dd>
            </div>
        </dl>

        <a
            href="{{ route('courses.show', $course['slug']) }}"
            class="mt-1 inline-flex items-center justify-center gap-1.5 rounded-full bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-500"
        >
            Lihat Detail
            <svg class="size-3.5 transition group-hover:translate-x-0.5" viewBox="0 0 16 14" fill="none"><path d="M7.5 0c0 .7.69 1.74 1.39 2.61.9 1.13 1.97 2.11 3.2 2.86.92.57 2.04 1.11 2.94 1.11M7.5 13.17c0-.7.69-1.74 1.39-2.61.9-1.13 1.97-2.11 3.2-2.86.92-.56 2.04-1.1 2.94-1.1M15.05 6.58H0" stroke="currentColor" stroke-width="1.5"/></svg>
        </a>
    </div>
</article>
