<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Tampilkan kuis untuk satu modul (soal + pilihan jawaban) dari data
     * asli (tabel quizzes/quiz_questions/quiz_options).
     */
    public function show(string $course, int $module)
    {
        [$courseModel, $moduleModel, $quizModel] = $this->findQuiz($course, $module);

        $quiz = [
            'title' => $quizModel->title,
            'description' => $quizModel->description,
            'passing_score' => $quizModel->passing_score,
            'questions' => $quizModel->questions->map(fn (QuizQuestion $question) => [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'score' => $question->score,
                'explanation' => $question->explanation,
                'options' => $question->options->map(fn ($option) => [
                    'id' => $option->id,
                    'option_text' => $option->option_text,
                    'is_correct' => $option->is_correct,
                ])->all(),
            ])->all(),
        ];

        return view('quizzes.show', [
            'course' => $courseModel,
            'module' => $moduleModel,
            'quiz' => $quiz,
        ]);
    }

    /**
     * Terima jawaban kuis, hitung nilai di server (jangan percaya skor
     * kiriman klien), lalu simpan sebagai satu percobaan kuis (quiz
     * attempt) beserta jawaban per soal.
     *
     * Body request: { answers: { "<question_id>": <selected_option_id>, ... } }
     * Balasan JSON dipakai Alpine.js di resources/views/quizzes/show.blade.php
     * untuk menampilkan nilai & pembahasan tanpa reload halaman.
     */
    public function submit(Request $request, string $course, int $module): JsonResponse
    {
        [, , $quizModel] = $this->findQuiz($course, $module);

        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Silakan daftar atau masuk terlebih dahulu untuk mengerjakan kuis.',
            ], 401);
        }

        $submittedAnswers = (array) $request->input('answers', []);

        $attempt = QuizAttempt::create([
            'quiz_id' => $quizModel->id,
            'user_id' => $user->id,
            'score' => 0,
            'total_score' => 100,
            'status' => 'completed',
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        $score = 0;
        $correctCount = 0;
        $breakdown = [];

        foreach ($quizModel->questions as $question) {
            $selectedOptionId = isset($submittedAnswers[$question->id])
                ? (int) $submittedAnswers[$question->id]
                : null;

            $selectedOption = $selectedOptionId
                ? $question->options->firstWhere('id', $selectedOptionId)
                : null;

            $isCorrect = (bool) $selectedOption?->is_correct;

            if ($isCorrect) {
                $score += $question->score;
                $correctCount++;
            }

            QuizAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'selected_option_id' => $selectedOptionId,
                'is_correct' => $isCorrect,
            ]);

            $breakdown[] = [
                'question_id' => $question->id,
                'selected_option_id' => $selectedOptionId,
                'is_correct' => $isCorrect,
                'correct_option_id' => $question->options->firstWhere('is_correct', true)?->id,
            ];
        }

        $attempt->update(['score' => $score]);

        return response()->json([
            'attempt_id' => $attempt->id,
            'score' => $score,
            'passing_score' => $quizModel->passing_score,
            'passed' => $score >= $quizModel->passing_score,
            'correct_count' => $correctCount,
            'total_questions' => $quizModel->questions->count(),
            'answers' => $breakdown,
        ]);
    }

    /**
     * Riwayat percobaan kuis milik pengguna yang sedang masuk, dipakai
     * Alpine.js di halaman dasbor (resources/views/components/quiz-history-list.blade.php)
     * lewat fetch — menggantikan versi lama yang membaca localStorage.
     * Route ini dilindungi middleware 'auth' (lihat routes/web.php).
     */
    public function history(Request $request): JsonResponse
    {
        $attempts = QuizAttempt::where('user_id', $request->user()->id)
            ->with(['quiz.module', 'quiz.course'])
            ->latest('finished_at')
            ->get()
            ->map(fn (QuizAttempt $attempt) => [
                'quiz_title' => $attempt->quiz->title,
                'course_title' => $attempt->quiz->course->title,
                'score' => $attempt->score,
                'passing_score' => $attempt->quiz->passing_score,
                'passed' => $attempt->score >= $attempt->quiz->passing_score,
                'finished_at' => $attempt->finished_at?->toIso8601String(),
            ]);

        return response()->json(['attempts' => $attempts]);
    }

    /**
     * Cari kursus, modul, dan kuis dari slug kursus + id modul.
     *
     * @return array{0: Course, 1: \App\Models\Module, 2: Quiz}
     */
    private function findQuiz(string $course, int $module): array
    {
        $courseModel = Course::published()
            ->where('slug', $course)
            ->with('modules')
            ->firstOrFail();

        $moduleModel = $courseModel->modules->firstWhere('id', $module);

        abort_if(! $moduleModel, 404);

        $quizModel = Quiz::where('module_id', $moduleModel->id)
            ->with('questions.options')
            ->first();

        abort_if(! $quizModel, 404);

        return [$courseModel, $moduleModel, $quizModel];
    }
}
