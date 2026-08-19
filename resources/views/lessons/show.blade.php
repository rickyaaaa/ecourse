@extends('layouts.app')

@section('title', $lesson->title . ' — ' . $course->title)

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    <nav class="mb-6 flex flex-wrap items-center gap-1 text-sm text-gray-500">
        <a href="{{ route('courses.index') }}" class="hover:text-indigo-600">Katalog Kursus</a>
        <span>/</span>
        <a href="{{ route('courses.show', $course->slug) }}" class="hover:text-indigo-600">{{ $course->title }}</a>
        <span>/</span>
        <span class="text-gray-700">{{ $module->title }}</span>
    </nav>

    <div
        class="grid grid-cols-1 gap-6 lg:grid-cols-[300px_1fr]"
        x-data="{
            completed: {{ $isCompleted ? 'true' : 'false' }},
            loading: false,
            toggle() {
                this.loading = true;

                fetch('{{ route('lessons.toggleComplete', [$course->slug, $lesson->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                })
                    .then((response) => (response.status === 401 ? null : response.json()))
                    .then((data) => {
                        if (data) {
                            this.completed = data.is_completed;
                        } else {
                            // Belum login — muat ulang supaya pesan "silakan masuk" tampil.
                            window.location.reload();
                        }
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },
        }"
    >
        <aside class="order-2 lg:order-1">
            <div class="rounded-xl border border-gray-200 bg-white p-4 lg:sticky lg:top-6">
                <p class="mb-3 truncate px-1 text-sm font-semibold text-gray-900">{{ $course->title }}</p>

                <div class="max-h-[70vh] space-y-4 overflow-y-auto pr-1">
                    @foreach ($modules as $syllabusModule)
                        <div>
                            <p class="mb-1 px-2 text-xs font-semibold uppercase tracking-wide text-gray-400">
                                {{ $syllabusModule->title }}
                            </p>
                            <ul class="space-y-0.5">
                                @foreach ($syllabusModule->lessons as $syllabusLesson)
                                    @php $isActive = $syllabusLesson->id === $lesson->id; @endphp
                                    <li>
                                        <a
                                            href="{{ route('lessons.show', [$course->slug, $syllabusLesson->id]) }}"
                                            class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm {{ $isActive ? 'bg-indigo-50 font-medium text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}"
                                        >
                                            <span aria-hidden="true">{{ $syllabusLesson->video_url ? '🎬' : '📄' }}</span>
                                            <span class="line-clamp-1 flex-1">{{ $syllabusLesson->title }}</span>
                                            @if ($syllabusLesson->id === $lesson->id)
                                                <span x-show="completed" x-cloak class="text-emerald-600" aria-hidden="true">✓</span>
                                            @elseif (in_array($syllabusLesson->id, $completedLessonIds, true))
                                                <span class="text-emerald-600" aria-hidden="true">✓</span>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                                <li>
                                    <a
                                        href="{{ route('quizzes.show', [$course->slug, $syllabusModule->id]) }}"
                                        class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm font-medium text-amber-700 hover:bg-amber-50"
                                    >
                                        <span aria-hidden="true">📝</span>
                                        <span class="line-clamp-1">Kerjakan Kuis Modul</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>

        <div class="order-1 lg:order-2">
            <div class="rounded-xl border border-gray-200 bg-white p-6 sm:p-8">
                <p class="text-sm font-medium text-indigo-600">{{ $module->title }}</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900 sm:text-3xl">{{ $lesson->title }}</h1>

                @if ($lesson->video_url)
                    <div class="mt-6 aspect-video overflow-hidden rounded-lg bg-black">
                        <iframe
                            src="{{ $lesson->embedUrl() }}"
                            title="{{ $lesson->title }}"
                            class="h-full w-full"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                        ></iframe>
                    </div>
                @else
                    {{-- Konten ditulis admin lewat editor materi (Filament), jadi HTML tepercaya --}}
                    <div class="prose prose-indigo mt-6 max-w-none prose-headings:font-semibold prose-a:text-indigo-600">
                        {!! $lesson->content !!}
                    </div>
                @endif

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    @if ($lesson->file_path)
                        <a
                            href="{{ route('lessons.download', [$course->slug, $lesson->id]) }}"
                            class="inline-flex items-center gap-2 rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            <span aria-hidden="true">📎</span>
                            Unduh Materi Pendukung
                        </a>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('lessons.toggleComplete', [$course->slug, $lesson->id]) }}"
                        @submit.prevent="toggle()"
                    >
                        @csrf
                        <button
                            type="submit"
                            :disabled="loading"
                            class="inline-flex items-center gap-2 rounded-md border px-4 py-2 text-sm font-medium transition disabled:cursor-wait disabled:opacity-60"
                            :class="completed
                                ? 'border-emerald-600 bg-emerald-50 text-emerald-700 hover:bg-emerald-100'
                                : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
                        >
                            <span x-text="completed ? '✅' : '⬜'" aria-hidden="true"></span>
                            <span x-text="completed ? 'Selesai' : 'Tandai Selesai'"></span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between gap-3">
                @if ($previousLesson)
                    <a
                        href="{{ route('lessons.show', [$course->slug, $previousLesson->id]) }}"
                        class="inline-flex items-center gap-1 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        &larr; Pelajaran Sebelumnya
                    </a>
                @else
                    <span></span>
                @endif

                @if ($nextLesson)
                    <a
                        href="{{ route('lessons.show', [$course->slug, $nextLesson->id]) }}"
                        class="inline-flex items-center gap-1 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500"
                    >
                        Pelajaran Berikutnya &rarr;
                    </a>
                @else
                    <a
                        href="{{ route('courses.show', $course->slug) }}"
                        class="inline-flex items-center gap-1 rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500"
                    >
                        Selesai — Kembali ke Silabus
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
