<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Halaman kelola kuis & soal di panel admin — CRUD asli ke tabel
 * quizzes/quiz_questions/quiz_options (Eloquent). Menggantikan versi
 * sebelumnya yang masih pakai data tiruan
 * (App\Support\MockData::quizForModule).
 */
class QuizController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::orderBy('title')->get(['id', 'title']);
        $selectedCourseId = (int) $request->query('course', $courses->first()?->id ?? 0);
        $selectedCourse = $courses->firstWhere('id', $selectedCourseId) ?? $courses->first();

        $modules = $selectedCourse
            ? Module::where('course_id', $selectedCourse->id)->orderBy('position')->get(['id', 'title'])
            : collect();

        $selectedModuleId = (int) $request->query('module', $modules->first()?->id ?? 0);
        $selectedModule = $modules->firstWhere('id', $selectedModuleId) ?? $modules->first();

        $quiz = $selectedModule
            ? Quiz::where('module_id', $selectedModule->id)->with('questions.options')->first()
            : null;

        return view('admin.quizzes.index', [
            'courses' => $courses,
            'selectedCourse' => $selectedCourse,
            'modules' => $modules,
            'selectedModule' => $selectedModule,
            'quiz' => $quiz,
        ]);
    }

    /**
     * Buat kuis kosong untuk modul yang belum punya kuis sama sekali.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'module_id' => ['required', 'integer', Rule::exists('modules', 'id')],
        ]);

        $module = Module::findOrFail($validated['module_id']);

        Quiz::firstOrCreate(
            ['module_id' => $module->id],
            [
                'course_id' => $module->course_id,
                'title' => "Kuis: {$module->title}",
                'description' => 'Uji pemahamanmu terhadap materi yang baru saja kamu pelajari di modul ini.',
                'passing_score' => 70,
            ],
        );

        return redirect()
            ->route('admin.quizzes.index', ['course' => $module->course_id, 'module' => $module->id])
            ->with('notice', 'Kuis untuk modul ini berhasil dibuat.');
    }

    public function update(Request $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'passing_score' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $quiz->update($validated);

        return redirect()
            ->route('admin.quizzes.index', ['course' => $quiz->course_id, 'module' => $quiz->module_id])
            ->with('notice', 'Pengaturan kuis berhasil disimpan.');
    }

    public function storeQuestion(Request $request, Quiz $quiz): RedirectResponse
    {
        $validated = $this->questionValidated($request);

        $question = $quiz->questions()->create([
            'question_text' => $validated['question_text'],
            'score' => $validated['score'],
            'explanation' => $validated['explanation'],
            'position' => $quiz->questions()->max('position') + 1,
        ]);

        $this->syncOptions($question, $validated['options'], $validated['correct_option']);

        return redirect()
            ->route('admin.quizzes.index', ['course' => $quiz->course_id, 'module' => $quiz->module_id])
            ->with('notice', 'Soal baru berhasil ditambahkan.');
    }

    public function updateQuestion(Request $request, Quiz $quiz, QuizQuestion $question): RedirectResponse
    {
        $validated = $this->questionValidated($request);

        $question->update([
            'question_text' => $validated['question_text'],
            'score' => $validated['score'],
            'explanation' => $validated['explanation'],
        ]);

        $this->syncOptions($question, $validated['options'], $validated['correct_option']);

        return redirect()
            ->route('admin.quizzes.index', ['course' => $quiz->course_id, 'module' => $quiz->module_id])
            ->with('notice', 'Perubahan soal berhasil disimpan.');
    }

    public function destroyQuestion(Quiz $quiz, QuizQuestion $question): RedirectResponse
    {
        $question->delete();

        return redirect()
            ->route('admin.quizzes.index', ['course' => $quiz->course_id, 'module' => $quiz->module_id])
            ->with('notice', 'Soal berhasil dihapus.');
    }

    /**
     * @return array{question_text: string, explanation: ?string, score: int, options: array<int, string>, correct_option: int}
     */
    private function questionValidated(Request $request): array
    {
        $data = $request->validate([
            'question_text' => ['required', 'string'],
            'explanation' => ['nullable', 'string'],
            'score' => ['required', 'integer', 'min:1', 'max:100'],
            'options' => ['required', 'array', 'min:2', 'max:6'],
            'options.*' => ['required', 'string', 'max:255'],
            'correct_option' => ['required', 'integer'],
        ]);

        if (! array_key_exists($data['correct_option'], $data['options'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'correct_option' => 'Pilih satu jawaban yang benar.',
            ]);
        }

        // 'nullable' cuma mengizinkan null KALAU field-nya dikirim — field
        // yang sama sekali tidak dikirim tetap tidak ada di $data, jadi
        // default-kan eksplisit di sini supaya aman diakses di pemanggil.
        $data['explanation'] = $data['explanation'] ?? null;

        return $data;
    }

    /**
     * Ganti seluruh pilihan jawaban soal dengan yang baru dikirim dari form
     * (lebih sederhana daripada diff satu-satu, dan soal kuis tidak punya
     * riwayat yang perlu dijaga per-opsi).
     *
     * @param  array<int, string>  $options
     */
    private function syncOptions(QuizQuestion $question, array $options, int $correctIndex): void
    {
        $question->options()->delete();

        foreach (array_values($options) as $index => $text) {
            $question->options()->create([
                'option_text' => $text,
                'is_correct' => $index === $correctIndex,
                'position' => $index + 1,
            ]);
        }
    }
}
