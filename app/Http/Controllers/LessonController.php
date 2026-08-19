<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LessonController extends Controller
{
    /**
     * Tampilkan satu halaman pelajaran (materi teks atau video), lengkap
     * dengan navigasi silabus (sidebar) serta tombol pelajaran sebelum/
     * sesudahnya.
     *
     * Progres ("tandai selesai") diambil dari tabel lesson_progress untuk
     * pengguna yang sedang masuk; kalau belum login, progres dianggap
     * kosong (belum ada yang tersimpan).
     */
    public function show(Request $request, string $course, int $lesson)
    {
        [$courseModel, $moduleModel, $lessonModel] = $this->findLesson($course, $lesson);

        $flatLessons = $courseModel->modules->flatMap(fn ($module) => $module->lessons);
        $currentIndex = $flatLessons->search(fn ($candidate) => $candidate->id === $lessonModel->id);

        $user = $request->user();
        $completedLessonIds = [];

        if ($user) {
            $completedLessonIds = LessonProgress::query()
                ->where('user_id', $user->id)
                ->whereIn('lesson_id', $flatLessons->pluck('id'))
                ->where('is_completed', true)
                ->pluck('lesson_id')
                ->all();

            // Catat kapan terakhir kali pelajaran ini diakses.
            LessonProgress::updateOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $lessonModel->id],
                ['last_accessed_at' => now()],
            );
        }

        return view('lessons.show', [
            'course' => $courseModel,
            'module' => $moduleModel,
            'lesson' => $lessonModel,
            'modules' => $courseModel->modules,
            'previousLesson' => $currentIndex > 0 ? $flatLessons[$currentIndex - 1] : null,
            'nextLesson' => $currentIndex < $flatLessons->count() - 1 ? $flatLessons[$currentIndex + 1] : null,
            'completedLessonIds' => $completedLessonIds,
            'isCompleted' => in_array($lessonModel->id, $completedLessonIds, true),
        ]);
    }

    /**
     * Tandai/batalkan tanda selesai untuk satu pelajaran (disimpan di
     * tabel lesson_progress).
     *
     * Endpoint ini melayani dua cara pakai (content negotiation berdasar
     * header Accept):
     * - Form biasa (tanpa JS): redirect kembali ke halaman pelajaran.
     * - Dipanggil lewat fetch/AJAX (Accept: application/json) dari Alpine.js
     *   di resources/views/lessons/show.blade.php: balas JSON supaya
     *   tombol & centang di sidebar bisa update instan tanpa reload.
     *
     * Kalau belum login: form biasa diarahkan kembali dengan pesan supaya
     * daftar/masuk dulu (sama seperti EnrollmentController); permintaan
     * JSON dibalas 401.
     */
    public function toggleComplete(Request $request, string $course, int $lesson): RedirectResponse|JsonResponse
    {
        [$courseModel, , $lessonModel] = $this->findLesson($course, $lesson);

        $user = $request->user();

        if (! $user) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Silakan daftar atau masuk terlebih dahulu untuk menandai pelajaran selesai.',
                ], 401);
            }

            return redirect()
                ->route('lessons.show', [$courseModel->slug, $lessonModel->id])
                ->with('notice', 'Silakan daftar atau masuk terlebih dahulu untuk menandai pelajaran selesai.');
        }

        $progress = LessonProgress::firstOrNew([
            'user_id' => $user->id,
            'lesson_id' => $lessonModel->id,
        ]);

        $progress->is_completed = ! $progress->is_completed;
        $progress->completed_at = $progress->is_completed ? now() : null;
        $progress->last_accessed_at = now();
        $progress->save();

        if ($request->wantsJson()) {
            return response()->json([
                'lesson_id' => $lessonModel->id,
                'is_completed' => $progress->is_completed,
            ]);
        }

        return redirect()->route('lessons.show', [$courseModel->slug, $lessonModel->id]);
    }

    /**
     * Unduh berkas lampiran pelajaran (mis. PDF ringkasan materi).
     */
    public function download(string $course, int $lesson)
    {
        [, , $lessonModel] = $this->findLesson($course, $lesson);

        abort_if(! $lessonModel->file_path, 404);
        abort_if(! Storage::disk('public')->exists($lessonModel->file_path), 404);

        $extension = pathinfo($lessonModel->file_path, PATHINFO_EXTENSION);
        $downloadName = Str::slug($lessonModel->title).($extension ? ".{$extension}" : '');

        return Storage::disk('public')->download($lessonModel->file_path, $downloadName);
    }

    /**
     * Cari kursus, modul, dan pelajaran dari slug kursus + id pelajaran,
     * memastikan pelajaran itu benar-benar milik kursus tersebut.
     *
     * @return array{0: Course, 1: Module, 2: Lesson}
     */
    private function findLesson(string $course, int $lesson): array
    {
        $courseModel = Course::published()
            ->where('slug', $course)
            ->with('modules.lessons')
            ->firstOrFail();

        foreach ($courseModel->modules as $candidateModule) {
            $found = $candidateModule->lessons->firstWhere('id', $lesson);

            if ($found) {
                return [$courseModel, $candidateModule, $found];
            }
        }

        abort(404);
    }
}
