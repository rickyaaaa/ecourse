@props(['course'])

@php
    $continueLabel = $course['progress'] >= 100 ? 'Ulas Kembali Kursus' : 'Lanjutkan Belajar';
@endphp

<div {{ $attributes->class(['rounded-xl border border-gray-200 bg-white p-5']) }}>
    <div class="flex items-center gap-3">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br {{ $course['thumbnail_color'] }} text-lg">
            {{ $course['thumbnail_icon'] }}
        </div>
        <div class="min-w-0">
            <p class="truncate font-medium text-gray-900">{{ $course['title'] }}</p>
            <p class="text-xs text-gray-500">{{ $course['category'] }}</p>
        </div>
    </div>

    <div class="mt-4">
        <div class="flex items-center justify-between text-xs text-gray-500">
            <span>Progres</span>
            <span>{{ $course['progress'] }}%</span>
        </div>
        <div class="mt-1 h-2 rounded-full bg-gray-100">
            <div class="h-2 rounded-full bg-indigo-600" style="width: {{ $course['progress'] }}%"></div>
        </div>
    </div>

    <a
        href="{{ $course['continue_url'] }}"
        class="mt-4 inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500"
    >
        {{ $continueLabel }}
    </a>
</div>
