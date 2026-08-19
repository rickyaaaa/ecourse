@props(['enrollment' => null, 'variant' => 'button', 'course' => null, 'continueUrl' => null])

@php
    $status = $enrollment['status'] ?? null;
@endphp

@if ($variant === 'badge')
    @if ($status === 'ongoing')
        <span class="absolute right-3 top-3 rounded-full bg-amber-500/90 px-2.5 py-1 text-xs font-semibold text-white shadow-sm">
            ▶ Sedang Diikuti
        </span>
    @elseif ($status === 'completed')
        <span class="absolute right-3 top-3 rounded-full bg-emerald-500/90 px-2.5 py-1 text-xs font-semibold text-white shadow-sm">
            ✅ Selesai
        </span>
    @endif
@else
    @if ($status === 'completed')
        <a
            href="{{ $continueUrl ?? '#' }}"
            class="inline-flex w-full items-center justify-center rounded-md border border-emerald-600 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 transition hover:bg-emerald-100"
        >
            ✅ Selesai — Ulangi Kursus
        </a>
        <p class="mt-2 text-xs text-emerald-700">Kamu sudah menyelesaikan kursus ini.</p>
    @elseif ($status === 'ongoing')
        <a
            href="{{ $continueUrl ?? '#' }}"
            class="inline-flex w-full items-center justify-center rounded-md bg-amber-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-amber-400"
        >
            ▶ Lanjutkan Belajar
        </a>

        @if (isset($enrollment['progress']))
            <div class="mt-3">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Progres</span>
                    <span>{{ $enrollment['progress'] }}%</span>
                </div>
                <div class="mt-1 h-2 rounded-full bg-gray-100">
                    <div class="h-2 rounded-full bg-amber-500" style="width: {{ $enrollment['progress'] }}%"></div>
                </div>
            </div>
        @endif
    @else
        <p class="text-sm text-gray-600">
            Daftar sekarang dan mulai belajar kapan saja, di mana saja.
        </p>

        @if ($course)
            <form method="POST" action="{{ route('enrollments.store', $course) }}">
                @csrf
                <button
                    type="submit"
                    class="mt-4 inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-500"
                >
                    Ikut Kursus
                </button>
            </form>
        @else
            <a
                href="#"
                class="mt-4 inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-500"
            >
                Ikut Kursus
            </a>
        @endif
    @endif
@endif
