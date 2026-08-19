<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\LessonProgress;
use App\Models\User;
use App\Support\CoursePresentation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /**
     * Tampilkan dasbor pelajar: kursus yang sedang diikuti (dengan progres
     * & tombol lanjutkan belajar) dan riwayat nilai kuis.
     *
     * Route ini dilindungi middleware 'auth' (lihat routes/web.php), jadi
     * $request->user() dijamin ada. Progres dihitung dari tabel
     * lesson_progress (data asli, sama yang diisi tombol "Tandai Selesai"
     * di halaman pelajaran). Riwayat nilai kuis diambil komponen
     * x-quiz-history-list lewat endpoint quizzes.history.
     */
    public function index(Request $request)
    {
        return view('dashboard.index', [
            'enrolledCourses' => $this->enrolledCoursesFor($request->user()),
        ]);
    }

    /**
     * Daftar lengkap kursus yang sedang diikuti pengguna (dengan progres
     * asli). Dibuka lewat browser (mis. klik "Kursus Saya" di menu) akan
     * menampilkan halaman HTML; dipanggil lewat fetch dengan header
     * Accept: application/json (dipakai komponen lain kalau perlu) akan
     * mengembalikan JSON seperti sebelumnya — jadi konsumen lama tidak
     * terpengaruh.
     */
    public function enrolledCourses(Request $request): JsonResponse|View
    {
        $courses = $this->enrolledCoursesFor($request->user());

        if ($request->wantsJson()) {
            return response()->json(['courses' => $courses->values()]);
        }

        return view('dashboard.enrolled-courses', ['enrolledCourses' => $courses]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function enrolledCoursesFor(User $user): Collection
    {
        $progressRows = LessonProgress::where('user_id', $user->id)->get();
        $completedLessonIds = $progressRows->where('is_completed', true)->pluck('lesson_id');
        // Peta lesson_id → kapan terakhir diakses, dipakai untuk "Lanjutkan
        // Belajar" (PRD: "menuju pelajaran terakhir yang sedang dipelajari").
        $lastAccessedAt = $progressRows->whereNotNull('last_accessed_at')->pluck('last_accessed_at', 'lesson_id');

        return $user->courses()
            ->with(['category', 'modules.lessons'])
            ->get()
            ->map(function (Course $course) use ($completedLessonIds, $lastAccessedAt) {
                $lessonIds = $course->modules->flatMap(fn ($module) => $module->lessons->pluck('id'))->values();
                $lessonsCount = $lessonIds->count();
                $completedCount = $lessonIds->intersect($completedLessonIds)->count();
                $progress = $lessonsCount > 0 ? (int) round(($completedCount / $lessonsCount) * 100) : 0;

                // 1) Pelajaran terakhir yang diakses di kursus ini (kalau ada).
                // 2) Kalau belum pernah membuka pelajaran apa pun, mulai dari
                //    pelajaran pertama yang belum selesai.
                $lastAccessedLessonId = $lessonIds
                    ->filter(fn ($id) => $lastAccessedAt->has($id))
                    ->sortByDesc(fn ($id) => $lastAccessedAt[$id])
                    ->first();

                $continueLessonId = $lastAccessedLessonId
                    ?? $lessonIds->first(fn ($id) => ! $completedLessonIds->contains($id))
                    ?? $lessonIds->first();

                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'slug' => $course->slug,
                    'category' => $course->category?->name,
                    'thumbnail_color' => CoursePresentation::thumbnailColor($course->category?->slug),
                    'thumbnail_icon' => CoursePresentation::thumbnailIcon($course->category?->slug),
                    'status' => $course->pivot->status,
                    'lessons_count' => $lessonsCount,
                    'completed_count' => $completedCount,
                    'progress' => $progress,
                    'continue_url' => $continueLessonId
                        ? route('lessons.show', [$course->slug, $continueLessonId])
                        : route('courses.show', $course->slug),
                ];
            })
            ->values();
    }
}
