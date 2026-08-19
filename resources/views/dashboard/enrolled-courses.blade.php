@extends('layouts.escul')

@section('title', 'Kursus Saya — Platform Kursus Online')

@section('content')
<x-escul.breadcrumb title="Kursus Saya" />

<div class="container py-5">
    @if ($enrolledCourses->isEmpty())
        <div class="text-center text-body p-5 border border-dashed rounded-4">
            Kamu belum mengikuti kursus apa pun.
            <a href="{{ route('courses.index') }}" class="fw-semibold">Jelajahi katalog kursus</a>
        </div>
    @else
        <p class="text-body mb-4">Kamu sedang mengikuti {{ $enrolledCourses->count() }} kursus.</p>
        <div class="row gy-4">
            @foreach ($enrolledCourses as $course)
                <div class="col-md-6 col-lg-4">
                    <x-my-course-card :course="$course" />
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
