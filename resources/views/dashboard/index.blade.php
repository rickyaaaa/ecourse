@extends('layouts.app')

@section('title', 'Dasbor Saya — Platform Kursus Online')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Dasbor Saya</h1>
    <p class="mt-2 text-gray-600">Pantau progres belajar dan riwayat nilai kuis kamu di sini.</p>

    @if ($enrolledCourses->isNotEmpty())
        <div class="mt-8">
            <h2 class="mb-4 text-xl font-semibold text-gray-900">Ringkasan Progres</h2>
            <x-progress-summary :courses="$enrolledCourses" />
        </div>
    @endif

    <div class="mt-8">
        <h2 class="mb-4 text-xl font-semibold text-gray-900">Kursus Saya</h2>

        @if ($enrolledCourses->isEmpty())
            <div class="rounded-lg border border-dashed border-gray-300 p-10 text-center text-gray-500">
                Kamu belum mengikuti kursus apa pun.
                <a href="{{ route('courses.index') }}" class="font-medium text-indigo-600 hover:underline">
                    Jelajahi katalog kursus
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($enrolledCourses as $course)
                    <x-my-course-card :course="$course" />
                @endforeach
            </div>
        @endif
    </div>

    <div class="mt-8">
        <h2 class="mb-4 text-xl font-semibold text-gray-900">Riwayat Nilai Kuis</h2>
        <x-quiz-history-list />
    </div>
</div>
@endsection
