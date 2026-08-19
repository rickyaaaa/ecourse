@extends('layouts.app')

@section('title', $quiz['title'] . ' — ' . $course->title)

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
    <nav class="mb-6 flex flex-wrap items-center gap-1 text-sm text-gray-500">
        <a href="{{ route('courses.index') }}" class="hover:text-indigo-600">Katalog Kursus</a>
        <span>/</span>
        <a href="{{ route('courses.show', $course->slug) }}" class="hover:text-indigo-600">{{ $course->title }}</a>
        <span>/</span>
        <span class="text-gray-700">{{ $module->title }}</span>
    </nav>

    <div
        class="rounded-xl border border-gray-200 bg-white p-6 sm:p-8"
        x-data="{
            answers: {},
            submitted: false,
            submitting: false,
            showIncompleteWarning: false,
            errorMessage: null,
            passingScore: {{ $quiz['passing_score'] }},
            totalQuestions: {{ count($quiz['questions']) }},
            score: 0,
            correctCount: 0,
            passed: false,
            get answeredCount() {
                return Object.keys(this.answers).length;
            },
            get isComplete() {
                return this.answeredCount === this.totalQuestions;
            },
            isSelected(questionId, optionId) {
                return Number(this.answers[questionId]) === optionId;
            },
            submit() {
                if (! this.isComplete) {
                    this.showIncompleteWarning = true;
                    return;
                }

                this.showIncompleteWarning = false;
                this.submitting = true;
                this.errorMessage = null;

                fetch('{{ route('quizzes.submit', [$course->slug, $module->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ answers: this.answers }),
                })
                    .then((response) => {
                        if (response.status === 401) {
                            this.errorMessage = 'Silakan daftar atau masuk terlebih dahulu untuk mengerjakan kuis.';
                            return null;
                        }

                        return response.json();
                    })
                    .then((data) => {
                        if (! data) return;

                        // Percobaan kuis ini sudah tersimpan otomatis di server
                        // (tabel quiz_attempts) oleh endpoint di atas — dasbor
                        // membaca riwayatnya lewat GET {{ route('quizzes.history') }}.
                        this.score = data.score;
                        this.correctCount = data.correct_count;
                        this.passed = data.passed;
                        this.submitted = true;
                    })
                    .catch(() => {
                        this.errorMessage = 'Gagal mengirim jawaban. Coba lagi.';
                    })
                    .finally(() => {
                        this.submitting = false;
                    });
            },
            retry() {
                this.answers = {};
                this.submitted = false;
                this.showIncompleteWarning = false;
                this.errorMessage = null;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
        }"
    >
        <p class="text-sm font-medium text-indigo-600">{{ $module->title }}</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900 sm:text-3xl">{{ $quiz['title'] }}</h1>
        <p class="mt-2 text-sm text-gray-600">{{ $quiz['description'] }}</p>
        <p class="mt-1 text-xs text-gray-500">Nilai kelulusan: {{ $quiz['passing_score'] }}</p>

        <form @submit.prevent="submit()" class="mt-8 space-y-8">
            @foreach ($quiz['questions'] as $index => $question)
                <fieldset :disabled="submitted || submitting" class="border-t border-gray-100 pt-6 first:border-t-0 first:pt-0">
                    <legend class="mb-3 font-medium text-gray-900">
                        {{ $index + 1 }}. {{ $question['question_text'] }}
                    </legend>

                    <div class="space-y-2">
                        @foreach ($question['options'] as $option)
                            <label class="flex cursor-pointer items-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                                <input
                                    type="radio"
                                    name="question-{{ $question['id'] }}"
                                    value="{{ $option['id'] }}"
                                    x-model="answers[{{ $question['id'] }}]"
                                    class="text-indigo-600 focus:ring-indigo-500"
                                >
                                {{ $option['option_text'] }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach

            <div x-show="!submitted">
                <p class="mb-2 text-sm text-gray-500" x-text="`${answeredCount} dari ${totalQuestions} soal terjawab`"></p>

                <p
                    x-show="showIncompleteWarning"
                    x-cloak
                    class="mb-2 text-sm font-medium text-red-600"
                >
                    Jawab semua soal dulu sebelum mengirim.
                </p>

                <p
                    x-show="errorMessage"
                    x-cloak
                    class="mb-2 text-sm font-medium text-red-600"
                    x-text="errorMessage"
                ></p>

                <button
                    type="submit"
                    :disabled="submitting"
                    class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-500 disabled:cursor-wait disabled:opacity-60"
                >
                    <span x-text="submitting ? 'Mengirim…' : 'Kirim Jawaban'"></span>
                </button>
            </div>

            <div x-show="submitted" x-cloak>
                <div
                    class="rounded-lg border px-6 py-5"
                    :class="passed ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50'"
                >
                    <p class="text-sm font-medium" :class="passed ? 'text-emerald-700' : 'text-amber-700'">
                        <span x-text="passed ? '✅ Selamat, kamu lulus!' : '⚠️ Belum lulus, coba lagi ya.'"></span>
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        <span x-text="score"></span><span class="text-base font-normal text-gray-500">/100</span>
                    </p>

                    <p class="mt-1 text-sm text-gray-600">
                        <span x-text="correctCount"></span> dari <span x-text="totalQuestions"></span> jawaban benar
                        &middot; nilai kelulusan <span x-text="passingScore"></span>
                    </p>

                    <button
                        type="button"
                        @click="retry()"
                        class="mt-4 inline-flex items-center gap-1 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        🔁 Ulangi Kuis
                    </button>
                </div>

                <div class="mt-6 space-y-6">
                    <h2 class="text-lg font-semibold text-gray-900">Pembahasan</h2>

                    @foreach ($quiz['questions'] as $index => $question)
                        <div class="rounded-lg border border-gray-200 p-4">
                            <p class="font-medium text-gray-900">{{ $index + 1 }}. {{ $question['question_text'] }}</p>

                            <div class="mt-3 space-y-1.5">
                                @foreach ($question['options'] as $option)
                                    <div
                                        class="rounded-md border px-3 py-2 text-sm"
                                        :class="{{ $option['is_correct'] ? 'true' : 'false' }}
                                            ? 'border-emerald-500 bg-emerald-50 text-emerald-800'
                                            : (isSelected({{ $question['id'] }}, {{ $option['id'] }}) ? 'border-red-500 bg-red-50 text-red-800' : 'border-gray-200 text-gray-600')"
                                    >
                                        {{ $option['option_text'] }}

                                        @if ($option['is_correct'])
                                            <span class="ml-1 font-medium">— Jawaban benar</span>
                                        @endif

                                        <span
                                            x-show="isSelected({{ $question['id'] }}, {{ $option['id'] }})"
                                            class="ml-1 italic"
                                        >(jawabanmu)</span>
                                    </div>
                                @endforeach
                            </div>

                            <p class="mt-3 rounded-md bg-indigo-50 px-3 py-2 text-sm text-indigo-800">
                                💡 {{ $question['explanation'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </form>
    </div>

    <a
        href="{{ route('courses.show', $course->slug) }}"
        class="mt-6 inline-flex items-center gap-1 text-sm font-medium text-gray-600 hover:text-indigo-600"
    >
        &larr; Kembali ke silabus kursus
    </a>
</div>
@endsection
