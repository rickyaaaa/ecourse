@extends('layouts.admin')

@section('title', 'Dasbor Admin — Platform Kursus Online')
@section('page-title', 'Dasbor')

@section('content')
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <p class="text-sm text-gray-500">Total Kursus</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['courses'] }}</p>
        <p class="mt-1 text-xs text-gray-400">{{ $stats['published_courses'] }} sudah diterbitkan</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <p class="text-sm text-gray-500">Total Pelajar</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['students'] }}</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <p class="text-sm text-gray-500">Total Pendaftaran Kursus</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['enrollments'] }}</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <p class="text-sm text-gray-500">Total Kuis</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['quizzes'] }}</p>
    </div>
</div>

<div class="mt-8 rounded-xl border border-gray-200 bg-white p-6">
    <h2 class="text-lg font-semibold text-gray-900">Selamat datang di Panel Admin</h2>
    <p class="mt-2 text-sm text-gray-600">
        Gunakan menu di samping untuk mengelola kursus, materi, kuis, dan peserta platform.
    </p>
</div>
@endsection
