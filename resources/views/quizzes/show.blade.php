@extends('layouts.escul')

@section('title', $quiz['title'] . ' — ' . $course->title)

@section('content')
<div class="container py-4" style="max-width:760px;">
    <nav class="mb-4 small text-body">
        <a href="{{ route('courses.index') }}" class="text-inherit">Katalog Kursus</a>
        <span class="mx-1">/</span>
        <a href="{{ route('courses.show', $course->slug) }}" class="text-inherit">{{ $course->title }}</a>
        <span class="mx-1">/</span>
        <span>{{ $module->title }}</span>
    </nav>

    <div
        class="bg-white border rounded-4 p-4 p-sm-5"
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
        <p class="fw-semibold mb-1" style="color:var(--theme-color);">{{ $module->title }}</p>
        <h1 class="h2 mb-2">{{ $quiz['title'] }}</h1>
        <p class="text-body mb-1">{{ $quiz['description'] }}</p>
        <p class="text-body small">Nilai kelulusan: {{ $quiz['passing_score'] }}</p>

        <form @submit.prevent="submit()" class="mt-4">
            @foreach ($quiz['questions'] as $index => $question)
                <fieldset :disabled="submitted || submitting" class="border-top pt-4 mt-4 {{ $loop->first ? 'border-0 pt-0 mt-0' : '' }}">
                    <legend class="mb-3 fw-semibold fs-6">
                        {{ $index + 1 }}. {{ $question['question_text'] }}
                    </legend>

                    <div class="d-flex flex-column gap-2">
                        @foreach ($question['options'] as $option)
                            <label
                                class="quiz-option-card d-flex align-items-center gap-2 mb-0"
                                :class="isSelected({{ $question['id'] }}, {{ $option['id'] }}) && 'is-selected'"
                            >
                                <input
                                    type="radio"
                                    name="question-{{ $question['id'] }}"
                                    value="{{ $option['id'] }}"
                                    x-model="answers[{{ $question['id'] }}]"
                                    class="form-check-input mt-0 flex-shrink-0"
                                >
                                {{ $option['option_text'] }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach

            <div x-show="!submitted" class="mt-4">
                <p class="text-body small mb-2" x-text="`${answeredCount} dari ${totalQuestions} soal terjawab`"></p>

                <p x-show="showIncompleteWarning" x-cloak class="text-danger small fw-semibold mb-2">
                    Jawab semua soal dulu sebelum mengirim.
                </p>

                <p x-show="errorMessage" x-cloak class="text-danger small fw-semibold mb-2" x-text="errorMessage"></p>

                <button type="submit" :disabled="submitting" class="th-btn">
                    <span x-text="submitting ? 'Mengirim…' : 'Kirim Jawaban'"></span>
                </button>
            </div>

            <div x-show="submitted" x-cloak class="mt-4">
                <div class="rounded-4 p-4" :class="passed ? 'bg-success-subtle' : 'bg-warning-subtle'">
                    <p class="fw-semibold mb-0">
                        <span x-text="passed ? '✅ Selamat, kamu lulus!' : '⚠️ Belum lulus, coba lagi ya.'"></span>
                    </p>

                    <p class="display-6 fw-bold mb-0 mt-2">
                        <span x-text="score"></span><span class="fs-5 fw-normal text-body">/100</span>
                    </p>

                    <p class="text-body small mb-0 mt-1">
                        <span x-text="correctCount"></span> dari <span x-text="totalQuestions"></span> jawaban benar
                        &middot; nilai kelulusan <span x-text="passingScore"></span>
                    </p>

                    <button type="button" @click="retry()" class="th-btn style-border2 btn-sm mt-3">
                        <i class="fal fa-rotate-right me-2"></i>Ulangi Kuis
                    </button>
                </div>

                <div class="mt-4">
                    <h2 class="h5 mb-3">Pembahasan</h2>

                    @foreach ($quiz['questions'] as $index => $question)
                        <div class="border rounded-4 p-3 mb-3">
                            <p class="fw-semibold mb-2">{{ $index + 1 }}. {{ $question['question_text'] }}</p>

                            <div class="d-flex flex-column gap-2">
                                @foreach ($question['options'] as $option)
                                    <div
                                        class="quiz-option-card"
                                        :class="{{ $option['is_correct'] ? 'true' : 'false' }}
                                            ? 'is-correct'
                                            : (isSelected({{ $question['id'] }}, {{ $option['id'] }}) ? 'is-incorrect' : '')"
                                    >
                                        {{ $option['option_text'] }}

                                        @if ($option['is_correct'])
                                            <span class="fw-semibold">— Jawaban benar</span>
                                        @endif

                                        <span x-show="isSelected({{ $question['id'] }}, {{ $option['id'] }})" class="fst-italic">(jawabanmu)</span>
                                    </div>
                                @endforeach
                            </div>

                            <p class="rounded-3 p-2 mt-2 mb-0 small quiz-explanation">
                                💡 {{ $question['explanation'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </form>
    </div>

    <a href="{{ route('courses.show', $course->slug) }}" class="d-inline-flex align-items-center gap-1 text-body small mt-3">
        <i class="fal fa-arrow-left"></i>Kembali ke silabus kursus
    </a>
</div>
@endsection
