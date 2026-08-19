@extends('layouts.escul')

@section('title', 'Dasbor Saya — Platform Kursus Online')

@section('content')
<div class="bg-smoke3 py-4 border-bottom">
    <div class="container">
        <h1 class="h3 mb-1">Halo, {{ Str::of(auth()->user()->name)->before(' ') }} 👋</h1>
        <p class="text-body mb-0">Pantau progres belajar dan riwayat nilai kuis kamu di sini.</p>
    </div>
</div>

<div class="container py-5">
    @if ($enrolledCourses->isNotEmpty())
        @php
            $totalLessons = $enrolledCourses->sum('lessons_count');
            $completedCount = $enrolledCourses->sum('completed_count');
            $overallProgress = $totalLessons > 0 ? (int) round(($completedCount / $totalLessons) * 100) : 0;
        @endphp
        <div class="row gy-3 mb-5">
            <div class="col-sm-4">
                <x-escul.stat-card icon="fa-book-open" label="Kursus Diikuti" :value="$enrolledCourses->count()" />
            </div>
            <div class="col-sm-4">
                <x-escul.stat-card icon="fa-check-double" label="Pelajaran Selesai" :value="$completedCount . '/' . $totalLessons" accent="theme2" />
            </div>
            <div class="col-sm-4">
                <x-escul.stat-card icon="fa-chart-line" label="Progres Keseluruhan" :value="$overallProgress . '%'" />
            </div>
        </div>
    @endif

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h4 mb-0">Kursus Saya</h2>
        @if ($enrolledCourses->isNotEmpty())
            <a href="{{ route('dashboard.enrolledCourses') }}" class="small fw-semibold">Lihat Semua <i class="fal fa-arrow-right ms-1"></i></a>
        @endif
    </div>

    @if ($enrolledCourses->isEmpty())
        <div class="text-center text-body p-5 border border-dashed rounded-4">
            Kamu belum mengikuti kursus apa pun.
            <a href="{{ route('courses.index') }}" class="fw-semibold">Jelajahi katalog kursus</a>
        </div>
    @else
        <div class="row gy-4 mb-5">
            @foreach ($enrolledCourses->take(6) as $course)
                <div class="col-md-6 col-lg-4">
                    <x-my-course-card :course="$course" />
                </div>
            @endforeach
        </div>
    @endif

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h4 mb-0">Riwayat Nilai Kuis</h2>
        <a href="{{ route('quizzes.history') }}" class="small fw-semibold">Lihat Semua <i class="fal fa-arrow-right ms-1"></i></a>
    </div>
    <x-quiz-history-list :limit="5" />
</div>
@endsection
