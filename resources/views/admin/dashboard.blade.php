@extends('layouts.admin')

@section('title', 'Dasbor Admin — Platform Kursus Online')
@section('page-title', 'Dasbor')

@section('content')
{{-- Quick actions --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h5 class="fw-bold mb-1">Halo, {{ auth()->user()->name }} 👋</h5>
        <p class="text-secondary-light mb-0">Ini ringkasan platform kursus hari ini.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.courses.index') }}" class="btn btn-primary-600 radius-8 d-flex align-items-center gap-1 px-16 py-8">
            <i class="ri-add-line"></i> Tambah Kursus
        </a>
        <a href="{{ route('admin.modules.index') }}" class="btn btn-outline-primary-600 radius-8 d-flex align-items-center gap-1 px-16 py-8">
            <i class="ri-add-line"></i> Tambah Materi
        </a>
        <a href="{{ route('admin.quizzes.index') }}" class="btn btn-outline-primary-600 radius-8 d-flex align-items-center gap-1 px-16 py-8">
            <i class="ri-add-line"></i> Tambah Kuis
        </a>
        <a href="{{ route('admin.participants.index') }}" class="btn btn-outline-primary-600 radius-8 d-flex align-items-center gap-1 px-16 py-8">
            <i class="ri-add-line"></i> Tambah Peserta
        </a>
    </div>
</div>

{{-- Kartu statistik utama --}}
<div class="row gy-4 mb-24">
    <div class="col-xxl-4 col-md-6">
        <div class="card p-3 radius-8 shadow-none border bg-gradient-dark-start-1 h-100">
            <div class="card-body p-0">
                <div class="d-flex align-items-center gap-2 mb-12">
                    <span class="w-48-px h-48-px bg-base text-pink text-2xl flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle">
                        <i class="ri-team-fill"></i>
                    </span>
                    <span class="fw-medium text-secondary-light text-lg">Total Peserta</span>
                </div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-8">
                    <h5 class="fw-semibold mb-0">{{ number_format($stats['students'], 0, ',', '.') }}</h5>
                    @if ($stats['students_trend'])
                        <p class="text-sm mb-0 d-flex align-items-center gap-8">
                            <span class="text-white px-1 rounded-2 fw-medium text-sm {{ $stats['students_trend']['direction'] === 'up' ? 'bg-success-main' : 'bg-danger-main' }}">
                                {{ $stats['students_trend']['direction'] === 'up' ? '+' : '-' }}{{ $stats['students_trend']['value'] ?? '' }}{{ $stats['students_trend']['value'] !== null ? '%' : 'Baru' }}
                            </span>
                            7 hari terakhir
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-4 col-md-6">
        <div class="card p-3 radius-8 shadow-none border bg-gradient-dark-start-2 h-100">
            <div class="card-body p-0">
                <div class="d-flex align-items-center gap-2 mb-12">
                    <span class="w-48-px h-48-px bg-base text-purple text-2xl flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle">
                        <i class="ri-book-open-fill"></i>
                    </span>
                    <span class="fw-medium text-secondary-light text-lg">Total Kursus</span>
                </div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-8">
                    <h5 class="fw-semibold mb-0">{{ $stats['courses'] }}</h5>
                    <p class="text-sm mb-0 text-secondary-light">{{ $stats['published_courses'] }} aktif</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-4 col-md-6">
        <div class="card p-3 radius-8 shadow-none border bg-gradient-dark-start-3 h-100">
            <div class="card-body p-0">
                <div class="d-flex align-items-center gap-2 mb-12">
                    <span class="w-48-px h-48-px bg-base text-info text-2xl flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle">
                        <i class="ri-file-list-3-fill"></i>
                    </span>
                    <span class="fw-medium text-secondary-light text-lg">Total Enrollment</span>
                </div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-8">
                    <h5 class="fw-semibold mb-0">{{ number_format($stats['enrollments'], 0, ',', '.') }}</h5>
                    @if ($stats['enrollments_trend'])
                        <p class="text-sm mb-0 d-flex align-items-center gap-8">
                            <span class="text-white px-1 rounded-2 fw-medium text-sm {{ $stats['enrollments_trend']['direction'] === 'up' ? 'bg-success-main' : 'bg-danger-main' }}">
                                {{ $stats['enrollments_trend']['direction'] === 'up' ? '+' : '-' }}{{ $stats['enrollments_trend']['value'] ?? '' }}{{ $stats['enrollments_trend']['value'] !== null ? '%' : 'Baru' }}
                            </span>
                            7 hari terakhir
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row gy-4 mb-24">
    <div class="col-md-4">
        <div class="card radius-8 border h-100 p-20">
            <p class="text-secondary-light mb-1">Kursus Aktif</p>
            <h5 class="fw-semibold mb-0">{{ $stats['published_courses'] }} <span class="text-secondary-light fw-normal text-md">/ {{ $stats['courses'] }}</span></h5>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card radius-8 border h-100 p-20">
            <p class="text-secondary-light mb-1">Total Kuis</p>
            <h5 class="fw-semibold mb-0">{{ $stats['quizzes'] }}</h5>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card radius-8 border h-100 p-20">
            <p class="text-secondary-light mb-1">Rata-rata Progres Peserta</p>
            <h5 class="fw-semibold mb-0">{{ $stats['average_progress'] !== null ? $stats['average_progress'].'%' : '—' }}</h5>
        </div>
    </div>
</div>

{{-- Grafik pendaftaran peserta --}}
<div class="card radius-8 border mb-24">
    <div class="card-body">
        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-16">
            <h6 class="fw-bold text-lg mb-0">Peserta Baru — 14 Hari Terakhir</h6>
        </div>
        <div id="registrationChart"></div>
    </div>
</div>

<div class="row gy-4">
    {{-- Enrollment terbaru --}}
    <div class="col-xxl-7">
        <div class="card radius-8 border h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-lg mb-0">Enrollment Terbaru</h6>
                <a href="{{ route('admin.participants.index') }}" class="text-primary-600 fw-medium d-flex align-items-center gap-1">
                    Lihat Semua <i class="ri-arrow-right-s-line"></i>
                </a>
            </div>
            <div class="card-body p-24">
                @if ($recentEnrollments->isEmpty())
                    <p class="text-secondary-light text-center mb-0 py-24">Belum ada pendaftaran kursus.</p>
                @else
                    <div class="table-responsive scroll-sm">
                        <table class="table bordered-table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Peserta</th>
                                    <th scope="col">Kursus</th>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentEnrollments as $enrollment)
                                    <tr>
                                        <td class="text-secondary-light">{{ $enrollment['participant'] ?? '—' }}</td>
                                        <td class="text-secondary-light">{{ $enrollment['course'] ?? '—' }}</td>
                                        <td class="text-secondary-light">{{ $enrollment['date']->translatedFormat('d M Y') }}</td>
                                        <td>
                                            @if ($enrollment['status'] === 'completed')
                                                <span class="bg-success-focus text-success-600 border border-success-main px-12 py-2 radius-4 fw-medium text-sm">Selesai</span>
                                            @else
                                                <span class="bg-warning-focus text-warning-600 border border-warning-main px-12 py-2 radius-4 fw-medium text-sm">Berlangsung</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Aktivitas terbaru --}}
    <div class="col-xxl-5">
        <div class="card radius-8 border h-100">
            <div class="card-header">
                <h6 class="fw-bold text-lg mb-0">Aktivitas Terbaru</h6>
            </div>
            <div class="card-body p-24">
                @if ($recentActivity->isEmpty())
                    <p class="text-secondary-light text-center mb-0 py-24">Belum ada aktivitas.</p>
                @else
                    <ul class="list-unstyled d-flex flex-column gap-16 mb-0">
                        @foreach ($recentActivity as $activity)
                            <li class="d-flex align-items-start gap-3">
                                <span class="w-36-px h-36-px flex-shrink-0 rounded-circle d-flex justify-content-center align-items-center
                                    {{ match ($activity['type']) {
                                        'registration' => 'bg-info-focus text-info-main',
                                        'enrollment' => 'bg-primary-50 text-primary-600',
                                        default => 'bg-success-focus text-success-main',
                                    } }}">
                                    <i class="{{ match ($activity['type']) {
                                        'registration' => 'ri-user-add-line',
                                        'enrollment' => 'ri-book-open-line',
                                        default => 'ri-clipboard-line',
                                    } }}"></i>
                                </span>
                                <div>
                                    <p class="mb-0 text-secondary-light">{{ $activity['label'] }}</p>
                                    <span class="text-sm text-neutral-400">{{ $activity['at']->diffForHumans() }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Kursus terpopuler --}}
<div class="card radius-8 border mt-24">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="fw-bold text-lg mb-0">Kursus Terpopuler</h6>
        <a href="{{ route('admin.courses.index') }}" class="text-primary-600 fw-medium d-flex align-items-center gap-1">
            Lihat Semua <i class="ri-arrow-right-s-line"></i>
        </a>
    </div>
    <div class="card-body p-24">
        @if ($topCourses->isEmpty())
            <p class="text-secondary-light text-center mb-0 py-24">Belum ada kursus.</p>
        @else
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Kursus</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Modul / Pelajaran</th>
                            <th scope="col">Peserta</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topCourses as $course)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="w-40-px h-40-px flex-shrink-0 d-flex justify-content-center align-items-center radius-8 bg-gradient-to-br {{ $course['thumbnail_color'] }} text-white">
                                            {{ $course['thumbnail_icon'] }}
                                        </span>
                                        <span class="fw-medium text-secondary-light">{{ $course['title'] }}</span>
                                    </div>
                                </td>
                                <td class="text-secondary-light">{{ $course['category'] ?? 'Tanpa kategori' }}</td>
                                <td class="text-secondary-light">{{ $course['modules_count'] }} modul · {{ $course['lessons_count'] }} pelajaran</td>
                                <td class="text-secondary-light">{{ number_format($course['students_count'], 0, ',', '.') }}</td>
                                <td>
                                    @if ($course['is_published'])
                                        <span class="bg-success-focus text-success-600 border border-success-main px-12 py-2 radius-4 fw-medium text-sm">Diterbitkan</span>
                                    @else
                                        <span class="bg-neutral-200 text-neutral-600 border border-neutral-400 px-12 py-2 radius-4 fw-medium text-sm">Draf</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('registrationChart');
        if (! el || typeof ApexCharts === 'undefined') return;

        new ApexCharts(el, {
            chart: { type: 'area', height: 280, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [{ name: 'Peserta Baru', data: {!! Js::from($registrationChart['values']) !!} }],
            xaxis: { categories: {!! Js::from($registrationChart['labels']) !!} },
            yaxis: { min: 0, forceNiceScale: true, labels: { formatter: (v) => Math.round(v) } },
            colors: ['#487FFF'],
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] } },
            dataLabels: { enabled: false },
            grid: { borderColor: '#E5E7EB' },
        }).render();
    });
</script>
@endpush
@endsection
