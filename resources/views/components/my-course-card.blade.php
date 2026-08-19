@props(['course'])

@php
    $continueLabel = $course['progress'] >= 100 ? 'Ulas Kembali Kursus' : 'Lanjutkan Belajar';
@endphp

<div {{ $attributes->class(['stat-card h-100']) }}>
    <div class="d-flex align-items-center gap-3">
        <div class="course-thumb-placeholder bg-gradient {{ $course['thumbnail_color'] }} text-white rounded-3 flex-shrink-0" style="width:48px;height:48px;font-size:1.3rem;">
            {{ $course['thumbnail_icon'] }}
        </div>
        <div class="min-w-0">
            <p class="fw-semibold mb-0 text-truncate">{{ $course['title'] }}</p>
            <p class="text-body small mb-0">{{ $course['category'] }}</p>
        </div>
    </div>

    <div class="mt-3">
        <div class="d-flex align-items-center justify-content-between small text-body mb-1">
            <span>Progres</span>
            <span>{{ $course['progress'] }}%</span>
        </div>
        <div class="lesson-progress-track">
            <div class="lesson-progress-fill" style="width: {{ $course['progress'] }}%"></div>
        </div>
    </div>

    <a href="{{ $course['continue_url'] }}" class="th-btn btn-sm w-100 justify-content-center mt-3">
        {{ $continueLabel }}
    </a>
</div>
