@extends('layouts.escul')

@section('title', $lesson->title . ' — ' . $course->title)

@section('content')
<div class="container py-4">
    <nav class="mb-4 small text-body">
        <a href="{{ route('courses.index') }}" class="text-inherit">Katalog Kursus</a>
        <span class="mx-1">/</span>
        <a href="{{ route('courses.show', $course->slug) }}" class="text-inherit">{{ $course->title }}</a>
        <span class="mx-1">/</span>
        <span>{{ $module->title }}</span>
    </nav>

    <div
        class="lesson-layout"
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
                            window.location.reload();
                        }
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },
        }"
    >
        <aside class="lesson-sidebar">
            <p class="fw-semibold px-3 pt-3 mb-2 text-truncate">{{ $course->title }}</p>

            @foreach ($modules as $syllabusModule)
                <div class="mb-2">
                    <p class="text-uppercase small text-body px-3 mb-1" style="font-size:11px;letter-spacing:.05em;">
                        {{ $syllabusModule->title }}
                    </p>
                    @foreach ($syllabusModule->lessons as $syllabusLesson)
                        @php $isActive = $syllabusLesson->id === $lesson->id; @endphp
                        <a
                            href="{{ route('lessons.show', [$course->slug, $syllabusLesson->id]) }}"
                            class="lesson-sidebar-link {{ $isActive ? 'is-active' : '' }} {{ in_array($syllabusLesson->id, $completedLessonIds, true) ? 'is-completed' : '' }}"
                        >
                            <i class="fal {{ $syllabusLesson->video_url ? 'fa-circle-play' : 'fa-file-lines' }}"></i>
                            <span class="text-truncate flex-grow-1">{{ $syllabusLesson->title }}</span>
                            @if ($isActive)
                                <i class="fal fa-circle-check lesson-sidebar-check" x-show="completed" x-cloak></i>
                            @elseif (in_array($syllabusLesson->id, $completedLessonIds, true))
                                <i class="fal fa-circle-check lesson-sidebar-check"></i>
                            @endif
                        </a>
                    @endforeach
                    <a href="{{ route('quizzes.show', [$course->slug, $syllabusModule->id]) }}" class="lesson-sidebar-link" style="color:var(--theme-color2);">
                        <i class="fal fa-clipboard-check"></i>
                        <span class="text-truncate">Kerjakan Kuis Modul</span>
                    </a>
                </div>
            @endforeach
        </aside>

        <div>
            <div class="bg-white border rounded-4 p-4 p-sm-5">
                <p class="fw-semibold mb-1" style="color:var(--theme-color);">{{ $module->title }}</p>
                <h1 class="h2 mb-4">{{ $lesson->title }}</h1>

                @if ($lesson->video_url)
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden bg-dark mb-4">
                        <iframe
                            src="{{ $lesson->embedUrl() }}"
                            title="{{ $lesson->title }}"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                        ></iframe>
                    </div>
                @else
                    {{-- Konten ditulis admin lewat editor materi (Filament), jadi HTML tepercaya --}}
                    <div class="lesson-content mb-4">
                        {!! $lesson->content !!}
                    </div>
                @endif

                <div class="d-flex flex-wrap align-items-center gap-3">
                    @if ($lesson->file_path)
                        <a href="{{ route('lessons.download', [$course->slug, $lesson->id]) }}" class="th-btn style-border2 btn-sm">
                            <i class="fal fa-paperclip me-2"></i>Unduh Materi Pendukung
                        </a>
                    @endif

                    <form method="POST" action="{{ route('lessons.toggleComplete', [$course->slug, $lesson->id]) }}" @submit.prevent="toggle()">
                        @csrf
                        <button
                            type="submit"
                            :disabled="loading"
                            class="th-btn btn-sm"
                            :class="completed ? '' : 'style-border2'"
                        >
                            <i class="fal" :class="completed ? 'fa-circle-check' : 'fa-circle'"></i>
                            <span x-text="completed ? ' Selesai' : ' Tandai Selesai'"></span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between gap-3 mt-4">
                @if ($previousLesson)
                    <a href="{{ route('lessons.show', [$course->slug, $previousLesson->id]) }}" class="th-btn style-border2 btn-sm">
                        <i class="fal fa-arrow-left me-2"></i>Sebelumnya
                    </a>
                @else
                    <span></span>
                @endif

                @if ($nextLesson)
                    <a href="{{ route('lessons.show', [$course->slug, $nextLesson->id]) }}" class="th-btn btn-sm">
                        Berikutnya<i class="fal fa-arrow-right ms-2"></i>
                    </a>
                @else
                    <a href="{{ route('courses.show', $course->slug) }}" class="th-btn btn-sm">
                        <i class="fal fa-circle-check me-2"></i>Selesai — Kembali ke Silabus
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
