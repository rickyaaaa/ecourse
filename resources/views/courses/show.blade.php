@extends('layouts.marketing')

@section('title', $course['title'] . ' — Platform Kursus Online')
@section('meta_description', Str::limit($course['description'], 150))

@section('content')
<section class="relative overflow-hidden bg-gradient-to-br {{ $course['thumbnail_color'] }}">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_15%_15%,rgba(255,255,255,0.18),transparent_50%)]"></div>

    <div class="relative mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8">
        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-white/85 transition hover:text-white">
            <svg class="size-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 15 7 10l5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Kembali ke Katalog Kursus
        </a>

        <div class="mt-5 flex items-center gap-2">
            <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-medium text-white backdrop-blur">
                {{ $course['category'] }}
            </span>
            @if (! empty($course['level']))
                <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-medium text-white backdrop-blur">
                    {{ $course['level'] }}
                </span>
            @endif
        </div>

        <h1 class="mt-4 text-balance font-display text-3xl font-bold tracking-[-0.02em] text-white sm:text-4xl">{{ $course['title'] }}</h1>
        <p class="mt-3 max-w-2xl text-pretty leading-relaxed text-white/90">{{ $course['description'] }}</p>

        <div class="mt-7 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-white/90">
            <span class="flex items-center gap-1.5">📚 {{ $course['modules_count'] }} modul</span>
            <span class="flex items-center gap-1.5">📝 {{ $course['lessons_count'] }} pelajaran</span>
            <span class="flex items-center gap-1.5">👥 {{ number_format($course['students_count'], 0, ',', '.') }} pelajar</span>
        </div>
    </div>
</section>

<div class="mx-auto grid max-w-5xl grid-cols-1 gap-8 px-4 py-12 sm:px-6 lg:grid-cols-3 lg:px-8">
    <div class="lg:col-span-2">
        <h2 class="mb-4 font-display text-xl font-semibold text-ink">Silabus Kursus</h2>

        <div class="divide-y divide-black/5 overflow-hidden rounded-2xl border border-black/5 bg-white shadow-sm">
            @foreach ($syllabus as $module)
                <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                    <button
                        type="button"
                        @click="open = !open"
                        class="flex w-full items-center justify-between gap-3 px-5 py-4 text-left transition hover:bg-brand-50/50"
                    >
                        <div class="flex items-center gap-3">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-700">
                                {{ $loop->iteration }}
                            </span>
                            <div>
                                <p class="font-medium text-ink">{{ $module['title'] }}</p>
                                <p class="text-xs text-ink-soft">{{ count($module['lessons']) }} pelajaran</p>
                            </div>
                        </div>
                        <svg class="size-4 shrink-0 text-ink-soft transition" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><path d="m5 7.5 5 5 5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>

                    <ul
                        x-show="open"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="space-y-1 px-5 pb-4 pl-16"
                    >
                        @foreach ($module['lessons'] as $lesson)
                            <li>
                                <a
                                    href="{{ route('lessons.show', [$course['slug'], $lesson['id']]) }}"
                                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-ink-soft transition hover:bg-brand-50 hover:text-brand-700"
                                >
                                    <span aria-hidden="true">{{ $lesson['type'] === 'video' ? '🎬' : '📄' }}</span>
                                    {{ $lesson['title'] }}
                                </a>
                            </li>
                        @endforeach
                        <li>
                            <a
                                href="{{ route('quizzes.show', [$course['slug'], $module['id']]) }}"
                                class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gold-700 transition hover:bg-gold-50"
                            >
                                <span aria-hidden="true">📝</span>
                                Kerjakan Kuis Modul Ini
                            </a>
                        </li>
                    </ul>
                </div>
            @endforeach
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="sticky top-24 rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <x-enroll-status :enrollment="$course['enrollment']" :course="$course['slug']" :continue-url="$course['continue_url']" />

            <dl class="mt-6 space-y-2 border-t border-black/5 pt-4 text-sm text-ink-soft">
                <div class="flex justify-between">
                    <dt>Kategori</dt>
                    <dd class="font-medium text-ink">{{ $course['category'] }}</dd>
                </div>
                @if (! empty($course['level']))
                    <div class="flex justify-between">
                        <dt>Level</dt>
                        <dd class="font-medium text-ink">{{ $course['level'] }}</dd>
                    </div>
                @endif
                <div class="flex justify-between">
                    <dt>Modul</dt>
                    <dd class="font-medium text-ink">{{ $course['modules_count'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt>Pelajaran</dt>
                    <dd class="font-medium text-ink">{{ $course['lessons_count'] }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
