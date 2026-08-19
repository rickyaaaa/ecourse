{{--
    Kartu kursus bergaya Escul (course-card), tapi seluruh datanya dinamis
    dari database (lihat App\Http\Controllers\CourseController::presentCourse)
    — tidak ada harga/rating/instruktur palsu karena data itu tidak ada di
    skema aplikasi ini.
--}}
@props(['course'])

@php
    $status = $course['enrollment']['status'] ?? null;
@endphp

<div {{ $attributes->class(['course-card h-100']) }}>
    <div class="box-img position-relative">
        <a href="{{ route('courses.show', $course['slug']) }}" class="d-block">
            <div class="course-thumb-placeholder bg-gradient {{ $course['thumbnail_color'] }} text-white">
                <span aria-hidden="true">{{ $course['thumbnail_icon'] }}</span>
            </div>
        </a>

        @if ($status === 'ongoing')
            <span class="course-status-badge is-ongoing"><i class="fal fa-play me-1"></i>Sedang Diikuti</span>
        @elseif ($status === 'completed')
            <span class="course-status-badge is-completed"><i class="fal fa-check me-1"></i>Selesai</span>
        @endif
    </div>

    <div class="d-flex align-items-center gap-2 mt-3">
        <span class="badge rounded-pill text-bg-light border">{{ $course['category'] }}</span>
        @if (! empty($course['level']))
            <span class="badge rounded-pill text-bg-light border">{{ $course['level'] }}</span>
        @endif
    </div>

    <h3 class="box-title"><a href="{{ route('courses.show', $course['slug']) }}">{{ $course['title'] }}</a></h3>

    @if (! empty($course['description']))
        <p class="box-text text-truncate-2">{{ $course['description'] }}</p>
    @endif

    <div class="box-content">
        <div class="course-info">
            <div class="box-icon"><i class="fal fa-book-open"></i></div>
            <div class="course-info-details">
                <span class="course-info-title">Modul:</span>
                <h4 class="course-info-text">{{ $course['modules_count'] }} modul</h4>
            </div>
        </div>
        <div class="course-info">
            <div class="box-icon"><i class="fal fa-file-lines"></i></div>
            <div class="course-info-details">
                <span class="course-info-title">Pelajaran:</span>
                <h4 class="course-info-text">{{ $course['lessons_count'] }} pelajaran</h4>
            </div>
        </div>
        <div class="course-info">
            <div class="box-icon"><i class="fal fa-users"></i></div>
            <div class="course-info-details">
                <span class="course-info-title">Pelajar:</span>
                <h4 class="course-info-text">{{ number_format($course['students_count'], 0, ',', '.') }} pelajar</h4>
            </div>
        </div>
    </div>

    <div class="btn-wrap justify-content-end">
        @if ($status && ! empty($course['continue_url']))
            <a href="{{ $course['continue_url'] }}" class="th-btn btn-sm">
                {{ $status === 'completed' ? 'Ulangi Kursus' : 'Lanjutkan Belajar' }}
            </a>
        @else
            <a href="{{ route('courses.show', $course['slug']) }}" class="th-btn btn-sm style-border2">LIHAT DETAIL</a>
        @endif
    </div>
</div>
