@extends('layouts.admin')

@section('title', 'Dasbor Admin — Platform Kursus Online')
@section('page-title', 'Dasbor')

@section('content')
<div class="mb-6 flex flex-col gap-4 md:mb-8 md:flex-row md:items-center md:justify-between">
    <div>
        <h2 class="mb-1 text-2xl font-bold text-admin-foreground md:text-3xl">Ringkasan Platform</h2>
        <p class="text-sm text-admin-secondary md:text-base">Halo, {{ auth()->user()->name }}! Ini kondisi platform hari ini.</p>
    </div>
    <div class="grid grid-cols-2 gap-2 md:flex md:w-auto md:items-center md:gap-3">
        <a href="{{ route('admin.participants.index') }}" class="flex items-center justify-center gap-2 rounded-admin-button px-4 py-3 text-sm font-semibold text-admin-foreground ring-1 ring-admin-border transition-all hover:ring-admin-primary md:px-6">
            <i data-lucide="user-plus" class="h-5 w-5"></i>
            <span>Tambah Peserta</span>
        </a>
        <a href="{{ route('admin.courses.index') }}" class="flex items-center justify-center gap-2 rounded-admin-button bg-admin-primary px-4 py-3 text-sm font-bold text-white transition-all hover:bg-admin-primary-hover md:px-6">
            <i data-lucide="plus" class="h-5 w-5"></i>
            <span>Tambah Kursus</span>
        </a>
    </div>
</div>

{{-- Stat cards --}}
<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 md:mb-8 md:gap-6 lg:grid-cols-4">
    <div class="flex flex-col gap-3 rounded-admin-card border border-admin-border bg-white p-6">
        <div class="flex items-center gap-2">
            <div class="flex size-11 shrink-0 items-center justify-center rounded-admin-icon bg-admin-primary/10">
                <i data-lucide="book-open" class="size-6 text-admin-primary"></i>
            </div>
            <p class="font-medium text-admin-secondary">Total Kursus</p>
        </div>
        <div class="flex items-baseline gap-2">
            <p class="text-[32px] font-bold leading-10">{{ $stats['courses'] }}</p>
            <span class="text-sm text-admin-secondary">{{ $stats['published_courses'] }} diterbitkan</span>
        </div>
    </div>

    <div class="flex flex-col gap-3 rounded-admin-card border border-admin-border bg-white p-6">
        <div class="flex items-center gap-2">
            <div class="flex size-11 shrink-0 items-center justify-center rounded-admin-icon bg-admin-info/10">
                <i data-lucide="users" class="size-6 text-admin-info"></i>
            </div>
            <p class="font-medium text-admin-secondary">Total Peserta</p>
        </div>
        <div class="flex items-center gap-3">
            <p class="text-[32px] font-bold leading-10">{{ number_format($stats['students'], 0, ',', '.') }}</p>
            @if ($stats['students_trend'])
                <span class="flex items-center text-sm font-semibold {{ $stats['students_trend']['direction'] === 'up' ? 'text-admin-success' : 'text-admin-error' }}">
                    <i data-lucide="{{ $stats['students_trend']['direction'] === 'up' ? 'arrow-up' : 'arrow-down' }}" class="mr-1 w-4 h-4"></i>
                    {{ $stats['students_trend']['value'] !== null ? $stats['students_trend']['value'].'%' : 'Baru' }}
                </span>
            @endif
        </div>
        <p class="text-xs text-admin-secondary">dibanding 7 hari sebelumnya</p>
    </div>

    <div class="flex flex-col gap-3 rounded-admin-card border border-admin-border bg-white p-6">
        <div class="flex items-center gap-2">
            <div class="flex size-11 shrink-0 items-center justify-center rounded-admin-icon bg-admin-success/10">
                <i data-lucide="book-user" class="size-6 text-admin-success"></i>
            </div>
            <p class="font-medium text-admin-secondary">Total Pendaftaran</p>
        </div>
        <div class="flex items-center gap-3">
            <p class="text-[32px] font-bold leading-10">{{ number_format($stats['enrollments'], 0, ',', '.') }}</p>
            @if ($stats['enrollments_trend'])
                <span class="flex items-center text-sm font-semibold {{ $stats['enrollments_trend']['direction'] === 'up' ? 'text-admin-success' : 'text-admin-error' }}">
                    <i data-lucide="{{ $stats['enrollments_trend']['direction'] === 'up' ? 'arrow-up' : 'arrow-down' }}" class="mr-1 w-4 h-4"></i>
                    {{ $stats['enrollments_trend']['value'] !== null ? $stats['enrollments_trend']['value'].'%' : 'Baru' }}
                </span>
            @endif
        </div>
        <p class="text-xs text-admin-secondary">{{ $stats['completed_enrollments'] }} sudah selesai</p>
    </div>

    <div class="flex flex-col gap-3 rounded-admin-card border border-admin-border bg-white p-6">
        <div class="flex items-center gap-2">
            <div class="flex size-11 shrink-0 items-center justify-center rounded-admin-icon bg-admin-warning/10">
                <i data-lucide="clipboard-list" class="size-6 text-admin-warning-dark"></i>
            </div>
            <p class="font-medium text-admin-secondary">Total Kuis</p>
        </div>
        <p class="text-[32px] font-bold leading-10">{{ $stats['quizzes'] }}</p>
    </div>
</div>

{{-- Grafik pendaftaran peserta --}}
<div class="mb-6 flex flex-col gap-6 rounded-admin-card border border-admin-border bg-white p-6 md:mb-8">
    <div>
        <h3 class="text-lg font-bold text-admin-foreground">Pendaftaran Peserta Baru</h3>
        <p class="text-sm text-admin-secondary">14 hari terakhir</p>
    </div>
    <div class="h-[280px] w-full">
        <canvas id="registrationChart"></canvas>
    </div>
</div>

{{-- Kursus populer & peserta terbaru --}}
<div class="grid grid-cols-1 gap-6 md:gap-8 lg:grid-cols-5">
    <div class="flex flex-col gap-4 rounded-admin-card border border-admin-border bg-white p-6 lg:col-span-3">
        <div class="flex items-center justify-between gap-3">
            <h3 class="text-lg font-bold text-admin-foreground">Kursus Populer</h3>
            <a href="{{ route('admin.courses.index') }}" class="text-sm font-semibold text-admin-primary hover:underline">Lihat Semua</a>
        </div>

        @if ($topCourses->isEmpty())
            <p class="rounded-xl border border-dashed border-admin-border p-8 text-center text-sm text-admin-secondary">
                Belum ada kursus.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px]">
                    <thead>
                        <tr class="border-b border-admin-border">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-admin-secondary">Kursus</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-admin-secondary">Modul</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-admin-secondary">Peserta</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-admin-secondary">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topCourses as $course)
                            <tr class="border-b border-admin-border last:border-0 hover:bg-admin-muted/50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $course['thumbnail_color'] }} text-base">
                                            {{ $course['thumbnail_icon'] }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-admin-foreground">{{ $course['title'] }}</p>
                                            <p class="truncate text-xs text-admin-secondary">{{ $course['category'] ?? 'Tanpa kategori' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-medium text-admin-foreground">
                                    {{ $course['modules_count'] }} modul · {{ $course['lessons_count'] }} pelajaran
                                </td>
                                <td class="px-4 py-3 font-medium text-admin-foreground">{{ number_format($course['students_count'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $course['is_published'] ? 'bg-admin-success-light text-admin-success-dark' : 'bg-admin-muted text-admin-secondary' }}">
                                        {{ $course['is_published'] ? 'Diterbitkan' : 'Draf' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="flex flex-col gap-4 rounded-admin-card border border-admin-border bg-white p-6 lg:col-span-2">
        <div class="flex items-center justify-between gap-3">
            <h3 class="text-lg font-bold text-admin-foreground">Peserta Terbaru</h3>
            <a href="{{ route('admin.participants.index') }}" class="text-sm font-semibold text-admin-primary hover:underline">Lihat Semua</a>
        </div>

        @if ($recentParticipants->isEmpty())
            <p class="rounded-xl border border-dashed border-admin-border p-8 text-center text-sm text-admin-secondary">
                Belum ada peserta.
            </p>
        @else
            <div class="flex flex-col gap-4">
                @foreach ($recentParticipants as $participant)
                    <div class="flex items-center gap-4">
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-admin-primary/10 text-sm font-semibold text-admin-primary">
                            {{ Str::of($participant['name'])->substr(0, 1)->upper() }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-admin-foreground">{{ $participant['name'] }}</p>
                            <p class="truncate text-sm text-admin-secondary">
                                {{ $participant['latest_course'] ? 'Mengikuti '.$participant['latest_course'] : 'Belum ikut kursus' }}
                            </p>
                        </div>
                        <span class="shrink-0 text-xs text-admin-secondary">{{ $participant['joined_at']->diffForHumans(['short' => true]) }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('registrationChart');
        if (! ctx || ! window.Chart) return;

        new window.Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! Js::from($registrationChart['labels']) !!},
                datasets: [{
                    label: 'Peserta Baru',
                    data: {!! Js::from($registrationChart['values']) !!},
                    borderColor: '#165DFF',
                    backgroundColor: 'rgba(22, 93, 255, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#165DFF',
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    tension: 0.4,
                    fill: true,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } },
                },
                plugins: { legend: { display: false } },
            },
        });
    });
</script>
@endpush
@endsection
