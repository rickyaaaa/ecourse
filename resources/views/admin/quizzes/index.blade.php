@extends('layouts.admin')

@section('title', 'Kelola Kuis — Panel Admin')
@section('page-title', 'Kelola Kuis')

@section('content')
<div class="mb-6">
    <h2 class="text-lg font-semibold text-gray-900">Kuis & Bank Soal</h2>
    <p class="mt-1 text-sm text-gray-500">Kelola kuis dan soal yang harus dijawab pelajar di akhir tiap modul.</p>
</div>

<form method="GET" action="{{ route('admin.quizzes.index') }}" class="mb-6 flex flex-col gap-3 sm:flex-row">
    <div class="sm:w-72">
        <label for="course" class="block text-sm font-medium text-gray-700">Pilih Kursus</label>
        <select
            id="course"
            name="course"
            x-data
            x-on:change="$el.form.requestSubmit()"
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
        >
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" @selected($selectedCourse && $selectedCourse->id === $course->id)>
                    {{ $course->title }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="sm:w-72">
        <label for="module" class="block text-sm font-medium text-gray-700">Pilih Modul</label>
        <select
            id="module"
            name="module"
            x-data
            x-on:change="$el.form.requestSubmit()"
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
        >
            @foreach ($modules as $module)
                <option value="{{ $module->id }}" @selected($selectedModule && $selectedModule->id === $module->id)>
                    {{ $module->title }}
                </option>
            @endforeach
        </select>
    </div>

    <noscript>
        <button type="submit" class="mt-6 h-fit rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
            Tampilkan
        </button>
    </noscript>
</form>

@if (! $selectedModule)
    <div class="rounded-lg border border-dashed border-gray-300 p-10 text-center text-gray-500">
        Belum ada modul untuk kursus ini. Tambahkan modul dulu di halaman Kelola Materi.
    </div>
@elseif (! $quiz)
    <div class="rounded-lg border border-dashed border-gray-300 p-10 text-center text-gray-500">
        <p>Modul ini belum punya kuis.</p>
        <form method="POST" action="{{ route('admin.quizzes.store') }}" class="mt-4 inline-block">
            @csrf
            <input type="hidden" name="module_id" value="{{ $selectedModule->id }}">
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                Buat Kuis untuk Modul Ini
            </button>
        </form>
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
                form.innerHTML = `
                    <input type=\"hidden\" name=\"_token\" value=\"${document.querySelector('meta[name=csrf-token]').content}\">
                    <input type=\"hidden\" name=\"_method\" value=\"DELETE\">
                `;
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
        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-500">Judul Kuis</p>
                    <p class="font-medium text-gray-900">{{ $quiz->title }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Nilai Kelulusan</p>
                    <p class="font-medium text-gray-900">{{ $quiz->passing_score }}</p>
                </div>
                <button @click="openEditQuiz()" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Ubah Pengaturan Kuis
                </button>
            </div>
        </div>

        <div class="mb-4 flex items-center justify-between">
            <h3 class="font-medium text-gray-900">Daftar Soal ({{ $quiz->questions->count() }})</h3>
            <button
                @click="openCreateQuestion()"
                class="inline-flex items-center justify-center gap-1 rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500"
            >
                + Tambah Soal
            </button>
        </div>

        <div class="space-y-3">
            @forelse ($quiz->questions as $index => $question)
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <div class="flex items-start justify-between gap-4">
                        <p class="font-medium text-gray-900">{{ $index + 1 }}. {{ $question->question_text }}</p>
                        <div class="flex shrink-0 items-center gap-3 text-sm">
                            <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
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
                                class="font-medium text-indigo-600 hover:underline"
                            >
                                Ubah
                            </button>
                            <button
                                @click="deleteQuestion({{ Js::from(['id' => $question->id]) }})"
                                class="font-medium text-red-600 hover:underline"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>

                    <ul class="mt-3 space-y-1.5">
                        @foreach ($question->options as $option)
                            <li class="flex items-center gap-2 text-sm {{ $option->is_correct ? 'text-green-700' : 'text-gray-600' }}">
                                <span>{{ $option->is_correct ? '✅' : '⬜' }}</span>
                                <span>{{ $option->option_text }}</span>
                            </li>
                        @endforeach
                    </ul>

                    @if ($question->explanation)
                        <p class="mt-3 text-xs text-gray-400">Pembahasan: {{ $question->explanation }}</p>
                    @endif
                </div>
            @empty
                <p class="rounded-lg border border-dashed border-gray-300 p-10 text-center text-gray-500">
                    Belum ada soal untuk kuis modul ini.
                </p>
            @endforelse
        </div>

        {{-- Modal ubah pengaturan kuis --}}
        <div x-show="showQuizModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div @click="showQuizModal = false" class="fixed inset-0 bg-gray-900/50"></div>

            <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900">Ubah Pengaturan Kuis</h3>

                <form method="POST" action="{{ route('admin.quizzes.update', $quiz) }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form_type" value="quiz">

                    <div>
                        <label for="quiz_title" class="block text-sm font-medium text-gray-700">Judul Kuis</label>
                        <input
                            type="text"
                            id="quiz_title"
                            name="title"
                            x-model="quizForm.title"
                            required
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                        @error('title')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="passing_score" class="block text-sm font-medium text-gray-700">Nilai Kelulusan</label>
                        <input
                            type="number"
                            id="passing_score"
                            name="passing_score"
                            x-model.number="quizForm.passing_score"
                            min="0"
                            max="100"
                            required
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                        @error('passing_score')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showQuizModal = false" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal tambah/ubah soal --}}
        <div x-show="showQuestionModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto px-4 py-8">
            <div @click="showQuestionModal = false" class="fixed inset-0 bg-gray-900/50"></div>

            <div class="relative w-full max-w-xl rounded-xl bg-white p-6 shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900" x-text="editingQuestionId === null ? 'Tambah Soal' : 'Ubah Soal'"></h3>

                <form
                    method="POST"
                    :action="editingQuestionId === null
                        ? '{{ route('admin.quizzes.questions.store', $quiz) }}'
                        : '{{ url('/admin/kuis/'.$quiz->id.'/soal') }}/' + editingQuestionId"
                    class="mt-4 space-y-4"
                >
                    @csrf
                    <input type="hidden" name="_method" :value="editingQuestionId === null ? 'POST' : 'PUT'">
                    <input type="hidden" name="_form_type" value="question">
                    <input type="hidden" name="_editing_id" :value="editingQuestionId">

                    <div>
                        <label for="question_text" class="block text-sm font-medium text-gray-700">Pertanyaan</label>
                        <textarea
                            id="question_text"
                            name="question_text"
                            x-model="questionForm.question_text"
                            rows="2"
                            required
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        ></textarea>
                        @error('question_text')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-gray-700">Pilihan Jawaban (tandai yang benar)</span>
                        <div class="mt-2 space-y-2">
                            <template x-for="(option, index) in questionForm.options" :key="index">
                                <div class="flex items-center gap-2">
                                    <input
                                        type="radio"
                                        name="correct_option"
                                        :value="index"
                                        :checked="questionForm.correctOption === index"
                                        @change="questionForm.correctOption = index"
                                        class="shrink-0 text-indigo-600 focus:ring-indigo-500"
                                    >
                                    <input
                                        type="text"
                                        name="options[]"
                                        x-model="questionForm.options[index]"
                                        :placeholder="`Pilihan ${index + 1}`"
                                        required
                                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    >
                                    <button
                                        type="button"
                                        @click="removeOptionRow(index)"
                                        x-show="questionForm.options.length > 2"
                                        x-cloak
                                        class="shrink-0 text-gray-400 hover:text-red-600"
                                        aria-label="Hapus pilihan"
                                    >
                                        ✕
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button
                            type="button"
                            @click="addOptionRow()"
                            x-show="questionForm.options.length < 6"
                            x-cloak
                            class="mt-2 text-sm font-medium text-indigo-600 hover:underline"
                        >
                            + Tambah pilihan
                        </button>
                        @error('options')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        @error('correct_option')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="question_score" class="block text-sm font-medium text-gray-700">Poin</label>
                        <input
                            type="number"
                            id="question_score"
                            name="score"
                            x-model.number="questionForm.score"
                            min="1"
                            max="100"
                            required
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                        @error('score')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="question_explanation" class="block text-sm font-medium text-gray-700">Pembahasan (opsional)</label>
                        <textarea
                            id="question_explanation"
                            name="explanation"
                            x-model="questionForm.explanation"
                            rows="2"
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showQuestionModal = false" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="! questionFormValid"
                            :class="questionFormValid ? 'bg-indigo-600 hover:bg-indigo-500' : 'bg-indigo-300'"
                            class="rounded-md px-4 py-2 text-sm font-medium text-white"
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
