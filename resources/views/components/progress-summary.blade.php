@props(['courses'])

@php
    $totalLessons = $courses->sum('lessons_count');
    $completedCount = $courses->sum('completed_count');
    $overallProgress = $totalLessons > 0 ? (int) round(($completedCount / $totalLessons) * 100) : 0;
@endphp

<div {{ $attributes->class(['rounded-xl border border-gray-200 bg-white p-5 sm:p-6']) }}>
    <div class="grid grid-cols-3 gap-3 sm:gap-6">
        <div>
            <p class="text-xs text-gray-500 sm:text-sm">Kursus Diikuti</p>
            <p class="mt-1 text-lg font-bold text-gray-900 sm:text-2xl">{{ $courses->count() }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 sm:text-sm">Pelajaran Selesai</p>
            <p class="mt-1 text-lg font-bold text-gray-900 sm:text-2xl">{{ $completedCount }}/{{ $totalLessons }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 sm:text-sm">Progres Keseluruhan</p>
            <p class="mt-1 text-lg font-bold text-indigo-600 sm:text-2xl">{{ $overallProgress }}%</p>
        </div>
    </div>

    <div class="mt-4 h-2 rounded-full bg-gray-100">
        <div class="h-2 rounded-full bg-indigo-600" style="width: {{ $overallProgress }}%"></div>
    </div>
</div>
