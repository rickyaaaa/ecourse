@props(['enrollment' => null, 'variant' => 'button', 'course' => null, 'continueUrl' => null])

@php
    $status = $enrollment['status'] ?? null;
@endphp

@if ($variant === 'badge')
    @if ($status === 'ongoing')
        <span class="course-status-badge is-ongoing"><i class="fal fa-play me-1"></i>Sedang Diikuti</span>
    @elseif ($status === 'completed')
        <span class="course-status-badge is-completed"><i class="fal fa-check me-1"></i>Selesai</span>
    @endif
@else
    @if ($status === 'completed')
        <a href="{{ $continueUrl ?? '#' }}" class="th-btn w-100 justify-content-center">
            <i class="fal fa-rotate-right me-2"></i>Ulangi Kursus
        </a>
        <p class="text-body small mt-3 mb-0"><i class="fal fa-circle-check text-success me-1"></i>Kamu sudah menyelesaikan kursus ini.</p>
    @elseif ($status === 'ongoing')
        <a href="{{ $continueUrl ?? '#' }}" class="th-btn w-100 justify-content-center">
            <i class="fal fa-play me-2"></i>Lanjutkan Belajar
        </a>

        @if (isset($enrollment['progress']))
            <div class="mt-3">
                <div class="d-flex align-items-center justify-content-between small text-body mb-1">
                    <span>Progres</span>
                    <span>{{ $enrollment['progress'] }}%</span>
                </div>
                <div class="lesson-progress-track">
                    <div class="lesson-progress-fill" style="width: {{ $enrollment['progress'] }}%"></div>
                </div>
            </div>
        @endif
    @else
        <p class="text-body small">
            Daftar sekarang dan mulai belajar kapan saja, di mana saja.
        </p>

        @if ($course)
            <form method="POST" action="{{ route('enrollments.store', $course) }}">
                @csrf
                <button type="submit" class="th-btn w-100 justify-content-center">
                    Ikut Kursus
                </button>
            </form>
        @else
            <a href="#" class="th-btn w-100 justify-content-center">
                Ikut Kursus
            </a>
        @endif
    @endif
@endif
