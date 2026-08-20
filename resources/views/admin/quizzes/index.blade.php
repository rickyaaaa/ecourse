@extends('layouts.admin')

@section('title', 'Kelola Kuis — Panel Admin')
@section('page-title', 'Kelola Kuis')

@section('content')
<div class="mb-24">
    <h5 class="fw-bold mb-1">Kuis & Bank Soal</h5>
    <p class="text-secondary-light mb-0">Kelola kuis dan soal yang harus dijawab pelajar di akhir tiap modul.</p>
</div>

<form method="GET" action="{{ route('admin.quizzes.index') }}" class="row g-3 mb-24">
    <div class="col-sm-6 col-md-4">
        <label for="course" class="form-label fw-medium">Pilih Kursus</label>
        <select id="course" name="course" x-data x-on:change="$el.form.requestSubmit()" class="form-select radius-8">
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" @selected($selectedCourse && $selectedCourse->id === $course->id)>
                    {{ $course->title }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-sm-6 col-md-4">
        <label for="module" class="form-label fw-medium">Pilih Modul</label>
        <select id="module" name="module" x-data x-on:change="$el.form.requestSubmit()" class="form-select radius-8">
            @foreach ($modules as $module)
                <option value="{{ $module->id }}" @selected($selectedModule && $selectedModule->id === $module->id)>
                    {{ $module->title }}
                </option>
            @endforeach
        </select>
    </div>

    <noscript>
        <div class="col-auto d-flex align-items-end">
            <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-8">Tampilkan</button>
        </div>
    </noscript>
</form>

@if (! $selectedModule)
    <div class="card radius-8 border">
        <div class="card-body text-center text-secondary-light py-40">
            Belum ada modul untuk kursus ini. Tambahkan modul dulu di halaman Kelola Materi.
        </div>
    </div>
@elseif (! $quiz)
    <div class="card radius-8 border">
        <div class="card-body text-center text-secondary-light py-40">
            <p>Modul ini belum punya kuis.</p>
            <form method="POST" action="{{ route('admin.quizzes.store') }}" class="d-inline-block">
                @csrf
                <input type="hidden" name="module_id" value="{{ $selectedModule->id }}">
                <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-8">Buat Kuis untuk Modul Ini</button>
            </form>
        </div>
    </div>
@else
    <div
        x-data="{
            showQuizModal: false,
            showQuestionModal: false,
            editingQuestionId: null,
            quizForm: { title: {{ Js::from($quiz->title) }}, passing_score: {{ Js::from($quiz->passing_score) }} },
            questionForm: { question_text: '', explanation: '', score: 10, options: [] },
            emptyOptionRow() {
                return '';
            },
            openEditQuiz() {
                this.quizForm = { title: {{ Js::from($quiz->title) }}, passing_score: {{ Js::from($quiz->passing_score) }} };
                this.showQuizModal = true;
            },
            openCreateQuestion() {
                this.editingQuestionId = null;
                this.questionForm = {
                    question_text: '', explanation: '', score: 10,
                    options: [this.emptyOptionRow(), this.emptyOptionRow(), this.emptyOptionRow(), this.emptyOptionRow()],
                    correctOption: 0,
                };
                this.showQuestionModal = true;
            },
            openEditQuestion(question) {
                this.editingQuestionId = question.id;
                this.questionForm = {
                    question_text: question.question_text,
                    explanation: question.explanation,
                    score: question.score,
                    options: question.options.map(o => o.option_text),
                    correctOption: question.options.findIndex(o => o.is_correct),
                };
                this.showQuestionModal = true;
            },
            addOptionRow() {
                if (this.questionForm.options.length >= 6) return;
                this.questionForm.options.push(this.emptyOptionRow());
            },
            removeOptionRow(index) {
                if (this.questionForm.options.length <= 2) return;
                this.questionForm.options.splice(index, 1);
                if (this.questionForm.correctOption >= this.questionForm.options.length) {
                    this.questionForm.correctOption = 0;
                }
            },
            get questionFormValid() {
                return this.questionForm.question_text.trim() !== ''
                    && this.questionForm.options.every(o => o.trim() !== '');
            },
            deleteQuestion(question) {
                if (! confirm('Hapus soal ini? Tindakan ini tidak bisa dibatalkan.')) return;
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/kuis/{{ $quiz->id }}/soal/${question.id}`;

                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = document.querySelector('meta[name=csrf-token]').content;
                form.appendChild(token);

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                document.body.appendChild(form);
                form.submit();
            },
        }"
        x-init="
            @if ($errors->any() && old('_form_type') === 'quiz')
                showQuizModal = true;
            @elseif ($errors->any() && old('_form_type') === 'question')
                editingQuestionId = {{ old('_editing_id') ? (int) old('_editing_id') : 'null' }};
                questionForm = {
                    question_text: {{ Js::from(old('question_text', '')) }},
                    explanation: {{ Js::from(old('explanation', '')) }},
                    score: {{ Js::from((int) old('score', 10)) }},
                    options: {{ Js::from(old('options', ['', ''])) }},
                    correctOption: {{ Js::from((int) old('correct_option', 0)) }},
                };
                showQuestionModal = true;
            @endif
        "
    >
        <div class="card radius-8 border mb-24">
            <div class="card-body p-24">
                <div class="d-flex flex-column flex-sm-row gap-4 align-items-sm-center justify-content-sm-between">
                    <div>
                        <p class="text-secondary-light mb-1">Judul Kuis</p>
                        <p class="fw-medium mb-0">{{ $quiz->title }}</p>
                    </div>
                    <div>
                        <p class="text-secondary-light mb-1">Nilai Kelulusan</p>
                        <p class="fw-medium mb-0">{{ $quiz->passing_score }}</p>
                    </div>
                    <button @click="openEditQuiz()" class="btn btn-outline-secondary radius-8 px-16 py-8 align-self-start">
                        Ubah Pengaturan Kuis
                    </button>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-16">
            <h6 class="fw-bold mb-0">Daftar Soal ({{ $quiz->questions->count() }})</h6>
            <button @click="openCreateQuestion()" class="btn btn-primary-600 radius-8 d-flex align-items-center gap-1 px-16 py-8">
                <i class="ri-add-line"></i> Tambah Soal
            </button>
        </div>

        <div class="d-flex flex-column gap-12">
            @forelse ($quiz->questions as $index => $question)
                <div class="card radius-8 border">
                    <div class="card-body p-20">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <p class="fw-medium mb-0">{{ $index + 1 }}. {{ $question->question_text }}</p>
                            <div class="d-flex flex-shrink-0 align-items-center gap-3 text-sm">
                                <span class="bg-neutral-200 text-neutral-600 px-12 py-2 radius-4 fw-medium text-sm">
                                    {{ $question->score }} poin
                                </span>
                                <button
                                    @click="openEditQuestion({{ Js::from([
                                        'id' => $question->id,
                                        'question_text' => $question->question_text,
                                        'explanation' => $question->explanation,
                                        'score' => $question->score,
                                        'options' => $question->options->map(fn ($o) => ['option_text' => $o->option_text, 'is_correct' => $o->is_correct])->all(),
                                    ]) }})"
                                    class="fw-medium text-primary-600 bg-transparent border-0"
                                >
                                    Ubah
                                </button>
                                <button
                                    @click="deleteQuestion({{ Js::from(['id' => $question->id]) }})"
                                    class="fw-medium text-danger-600 bg-transparent border-0"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>

                        <ul class="list-unstyled d-flex flex-column gap-6 mt-12 mb-0">
                            @foreach ($question->options as $option)
                                <li class="d-flex align-items-center gap-2 text-sm {{ $option->is_correct ? 'text-success-600 fw-medium' : 'text-secondary-light' }}">
                                    <i class="{{ $option->is_correct ? 'ri-checkbox-circle-fill' : 'ri-checkbox-blank-circle-line' }}"></i>
                                    <span>{{ $option->option_text }}</span>
                                </li>
                            @endforeach
                        </ul>

                        @if ($question->explanation)
                            <p class="text-sm text-neutral-400 mt-12 mb-0">Pembahasan: {{ $question->explanation }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card radius-8 border">
                    <div class="card-body text-center text-secondary-light py-40">
                        Belum ada soal untuk kuis modul ini.
                    </div>
                </div>
            @endforelse
        </div>

        {{--
            Modal ubah pengaturan kuis. Div x-show/x-cloak SENGAJA tidak
            diberi class d-flex (lihat catatan panjang di
            admin/courses/index.blade.php) — Bootstrap men-set
            display:...!important pada class display-*, mengalahkan x-show
            Alpine maupun style="display:none" statis. Layout flex-center
            dipindah ke div pembungkus terpisah yang tidak dikontrol x-show.
        --}}
        <div x-show="showQuizModal" x-cloak class="position-fixed top-0 start-0 w-100 h-100" style="display:none;z-index:1050;">
            <div @click="showQuizModal = false" class="position-fixed top-0 start-0 w-100 h-100 bg-dark" style="opacity:.5;"></div>

            <div class="d-flex align-items-center justify-content-center w-100 h-100 p-16">
            <div class="position-relative bg-white radius-8 shadow w-100 p-24" style="max-width:480px;">
                <h6 class="fw-bold mb-16">Ubah Pengaturan Kuis</h6>

                <form method="POST" action="{{ route('admin.quizzes.update', $quiz) }}" class="d-flex flex-column gap-16">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form_type" value="quiz">

                    <div>
                        <label for="quiz_title" class="form-label fw-medium">Judul Kuis</label>
                        <input type="text" id="quiz_title" name="title" x-model="quizForm.title" required class="form-control radius-8">
                        @error('title')
                            <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="passing_score" class="form-label fw-medium">Nilai Kelulusan</label>
                        <input type="number" id="passing_score" name="passing_score" x-model.number="quizForm.passing_score" min="0" max="100" required class="form-control radius-8">
                        @error('passing_score')
                            <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-3 pt-2">
                        <button type="button" @click="showQuizModal = false" class="btn btn-outline-secondary radius-8 px-16 py-8">Batal</button>
                        <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-8">Simpan</button>
                    </div>
                </form>
            </div>
            </div>
        </div>

        {{-- Modal tambah/ubah soal (lihat catatan d-flex di modal pengaturan kuis di atas) --}}
        <div x-show="showQuestionModal" x-cloak class="position-fixed top-0 start-0 w-100 h-100" style="display:none;z-index:1050;">
            <div @click="showQuestionModal = false" class="position-fixed top-0 start-0 w-100 h-100 bg-dark" style="opacity:.5;"></div>

            <div class="d-flex align-items-center justify-content-center w-100 h-100 p-16">
            <div class="position-relative bg-white radius-8 shadow w-100 p-24" style="max-width:600px;max-height:90vh;overflow-y:auto;">
                <h6 class="fw-bold mb-16" x-text="editingQuestionId === null ? 'Tambah Soal' : 'Ubah Soal'"></h6>

                <form
                    method="POST"
                    :action="editingQuestionId === null
                        ? '{{ route('admin.quizzes.questions.store', $quiz) }}'
                        : '{{ url('/admin/kuis/'.$quiz->id.'/soal') }}/' + editingQuestionId"
                    class="d-flex flex-column gap-16"
                >
                    @csrf
                    <input type="hidden" name="_method" :value="editingQuestionId === null ? 'POST' : 'PUT'">
                    <input type="hidden" name="_form_type" value="question">
                    <input type="hidden" name="_editing_id" :value="editingQuestionId">

                    <div>
                        <label for="question_text" class="form-label fw-medium">Pertanyaan</label>
                        <textarea id="question_text" name="question_text" x-model="questionForm.question_text" rows="2" required class="form-control radius-8"></textarea>
                        @error('question_text')
                            <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <span class="form-label fw-medium d-block">Pilihan Jawaban (tandai yang benar)</span>
                        <div class="d-flex flex-column gap-8">
                            <template x-for="(option, index) in questionForm.options" :key="index">
                                <div class="d-flex align-items-center gap-2">
                                    <input
                                        type="radio"
                                        name="correct_option"
                                        :value="index"
                                        :checked="questionForm.correctOption === index"
                                        @change="questionForm.correctOption = index"
                                        class="form-check-input flex-shrink-0 mt-0"
                                    >
                                    <input
                                        type="text"
                                        name="options[]"
                                        x-model="questionForm.options[index]"
                                        :placeholder="`Pilihan ${index + 1}`"
                                        required
                                        class="form-control radius-8"
                                    >
                                    <button
                                        type="button"
                                        @click="removeOptionRow(index)"
                                        x-show="questionForm.options.length > 2"
                                        x-cloak
                                        class="flex-shrink-0 text-neutral-400 bg-transparent border-0"
                                        aria-label="Hapus pilihan"
                                    >
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button
                            type="button"
                            @click="addOptionRow()"
                            x-show="questionForm.options.length < 6"
                            x-cloak
                            class="mt-8 text-sm fw-medium text-primary-600 bg-transparent border-0"
                        >
                            + Tambah pilihan
                        </button>
                        @error('options')
                            <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                        @enderror
                        @error('correct_option')
                            <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="question_score" class="form-label fw-medium">Poin</label>
                        <input type="number" id="question_score" name="score" x-model.number="questionForm.score" min="1" max="100" required class="form-control radius-8">
                        @error('score')
                            <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="question_explanation" class="form-label fw-medium">Pembahasan (opsional)</label>
                        <textarea id="question_explanation" name="explanation" x-model="questionForm.explanation" rows="2" class="form-control radius-8"></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-3 pt-2">
                        <button type="button" @click="showQuestionModal = false" class="btn btn-outline-secondary radius-8 px-16 py-8">Batal</button>
                        <button
                            type="submit"
                            :disabled="! questionFormValid"
                            class="btn btn-primary-600 radius-8 px-16 py-8"
                            :class="! questionFormValid && 'opacity-50'"
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
@endif
@endsection
