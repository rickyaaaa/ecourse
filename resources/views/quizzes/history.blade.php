@extends('layouts.escul')

@section('title', 'Riwayat Kuis — Platform Kursus Online')

@section('content')
<x-escul.breadcrumb title="Riwayat Kuis" />

<div class="container py-5">
    @if ($attempts->isEmpty())
        <div class="text-center text-body p-5 border border-dashed rounded-4">
            Kamu belum mengerjakan kuis apa pun. Yuk mulai belajar dan coba kuisnya!
            <br>
            <a href="{{ route('courses.index') }}" class="fw-semibold">Jelajahi katalog kursus</a>
        </div>
    @else
        <p class="text-body mb-4">{{ $attempts->count() }} percobaan kuis tercatat.</p>
        <div class="table-responsive">
            <table class="table align-middle bg-white rounded-4 overflow-hidden mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kuis</th>
                        <th class="d-none d-sm-table-cell">Kursus</th>
                        <th>Nilai</th>
                        <th>Status</th>
                        <th class="d-none d-md-table-cell">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attempts as $attempt)
                        <tr>
                            <td>
                                {{ $attempt['quiz_title'] }}
                                <span class="d-block small text-body d-sm-none">{{ $attempt['course_title'] }}</span>
                            </td>
                            <td class="d-none d-sm-table-cell text-body">{{ $attempt['course_title'] }}</td>
                            <td class="fw-semibold">{{ $attempt['score'] }}/100</td>
                            <td>
                                <span class="badge rounded-pill {{ $attempt['passed'] ? 'text-bg-success' : 'text-bg-warning' }}">
                                    {{ $attempt['passed'] ? 'Lulus' : 'Belum Lulus' }}
                                </span>
                            </td>
                            <td class="d-none d-md-table-cell text-body">
                                {{ $attempt['finished_at'] ? \Illuminate\Support\Carbon::parse($attempt['finished_at'])->translatedFormat('d M Y, H:i') : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
