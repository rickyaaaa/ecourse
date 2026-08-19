<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Support\MockData;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Seed satu kuis per modul, dengan soal & pilihan jawaban dari bank
     * soal tiruan (App\Support\MockData::quizQuestionBank). Membutuhkan
     * CourseSeeder sudah dijalankan lebih dulu (perlu modules).
     */
    public function run(): void
    {
        $bank = MockData::quizQuestionBank();
        $questionCount = count($bank);
        $baseScore = intdiv(100, $questionCount);
        $remainder = 100 % $questionCount;

        Module::each(function (Module $module) use ($bank, $questionCount, $baseScore, $remainder) {
            $quiz = Quiz::updateOrCreate(
                ['course_id' => $module->course_id, 'module_id' => $module->id],
                [
                    'title' => "Kuis: {$module->title}",
                    'description' => 'Uji pemahamanmu terhadap materi yang baru saja kamu pelajari di modul ini.',
                    'passing_score' => 70,
                ],
            );

            foreach ($bank as $index => $template) {
                $question = QuizQuestion::updateOrCreate(
                    ['quiz_id' => $quiz->id, 'question_text' => $template['question_text']],
                    [
                        // Soal terakhir menampung sisa pembagian supaya total pas 100.
                        'score' => $baseScore + ($index === $questionCount - 1 ? $remainder : 0),
                        'explanation' => $template['explanation'],
                        'position' => $index + 1,
                    ],
                );

                foreach (array_values($template['options']) as $optionIndex => $option) {
                    QuizOption::updateOrCreate(
                        ['question_id' => $question->id, 'option_text' => $option['text']],
                        [
                            'is_correct' => $option['is_correct'],
                            'position' => $optionIndex + 1,
                        ],
                    );
                }
            }
        });
    }
}
