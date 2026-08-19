<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use App\Support\CoursePresentation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CourseController extends Controller
{
    /**
     * Tampilkan katalog kursus, dengan pencarian (judul/deskripsi) dan
     * filter kategori lewat query string (?search=&category=).
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $categorySlug = (string) $request->query('category', '');

        $courses = Course::query()
            ->published()
            ->with('category')
            ->withCount([
                'modules',
                'lessons',
                'enrollments as students_count',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($categorySlug !== '', function ($query) use ($categorySlug) {
                $query->whereHas('category', fn ($query) => $query->where('slug', $categorySlug));
            })
            ->when($request->user(), function ($query) use ($request) {
                $query->with(['enrollments' => fn ($query) => $query->where('user_id', $request->user()->id)]);
            })
            ->orderBy('title')
            ->paginate(6)
            ->withQueryString()
            ->through(fn (Course $course) => $this->presentCourse($course));

        $categories = Category::orderBy('name')->get();

        return view('courses.index', [
            'courses' => $courses,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $categorySlug,
            'stats' => [
                'courses' => Course::published()->count(),
                'categories' => $categories->count(),
                'students' => Enrollment::distinct('user_id')->count('user_id'),
            ],
        ]);
    }

    /**
     * Bentuk array presentasi satu kursus untuk view/komponen Blade.
     */
    private function presentCourse(Course $course): array
    {
        $enrollment = $course->relationLoaded('enrollments') ? $course->enrollments->first() : null;
        $progress = $enrollment ? $this->enrollmentProgress($course, $enrollment) : null;

        return [
            'id' => $course->id,
            'category_id' => $course->category_id,
            'category' => $course->category?->name,
            'title' => $course->title,
            'slug' => $course->slug,
            'description' => (string) $course->description,
            'level' => $course->level,
            'thumbnail_color' => CoursePresentation::thumbnailColor($course->category?->slug),
            'thumbnail_icon' => CoursePresentation::thumbnailIcon($course->category?->slug),
            'modules_count' => $course->modules_count,
            'lessons_count' => $course->lessons_count,
            'students_count' => $course->students_count,
            'is_published' => $course->is_published,
            'enrollment' => $enrollment ? [
                'status' => $enrollment->status,
                'progress' => $progress['progress'],
            ] : null,
            'continue_url' => $progress['continue_url'] ?? null,
        ];
    }

    /**
     * Progres (%) & URL "Lanjutkan Belajar"/"Ulangi Kursus" untuk satu
     * enrollment: pelajaran terakhir yang diakses kalau ada, kalau belum
     * pernah lanjut ke pelajaran pertama yang belum selesai, atau
     * pelajaran pertama kursus (sama seperti logika di
     * DashboardController::enrolledCoursesFor).
     *
     * @return array{progress: int, continue_url: ?string}
     */
    private function enrollmentProgress(Course $course, Enrollment $enrollment): array
    {
        $lessonIds = $course->modules()->with('lessons')->get()
            ->flatMap(fn (Module $module) => $module->lessons->pluck('id'))
            ->values();

        if ($lessonIds->isEmpty()) {
            return ['progress' => 0, 'continue_url' => null];
        }

        $progressRows = LessonProgress::where('user_id', $enrollment->user_id)
            ->whereIn('lesson_id', $lessonIds)
            ->get();

        $completedLessonIds = $progressRows->where('is_completed', true)->pluck('lesson_id');
        $lastAccessedAt = $progressRows->whereNotNull('last_accessed_at')->pluck('last_accessed_at', 'lesson_id');

        $lastAccessedLessonId = $lessonIds
            ->filter(fn ($id) => $lastAccessedAt->has($id))
            ->sortByDesc(fn ($id) => $lastAccessedAt[$id])
            ->first();

        $continueLessonId = $lastAccessedLessonId
            ?? $lessonIds->first(fn ($id) => ! $completedLessonIds->contains($id))
            ?? $lessonIds->first();

        $progress = (int) round(($lessonIds->intersect($completedLessonIds)->count() / $lessonIds->count()) * 100);

        return [
            'progress' => $progress,
            'continue_url' => route('lessons.show', [$course->slug, $continueLessonId]),
        ];
    }

    /**
     * Tampilkan detail satu kursus beserta silabusnya (modul & pelajaran).
     */
    public function show(string $course, Request $request)
    {
        $courseModel = Course::query()
            ->published()
            ->with('category')
            ->withCount([
                'modules',
                'lessons',
                'enrollments as students_count',
            ])
            ->when($request->user(), function ($query) use ($request) {
                $query->with(['enrollments' => fn ($query) => $query->where('user_id', $request->user()->id)]);
            })
            ->where('slug', $course)
            ->first();

        abort_if(! $courseModel, Response::HTTP_NOT_FOUND);

        $syllabus = $courseModel->modules()
            ->with('lessons')
            ->get()
            ->map(fn (Module $module) => [
                'id' => $module->id,
                'title' => $module->title,
                'lessons' => $module->lessons->map(fn (Lesson $lesson) => [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'type' => $lesson->video_url ? 'video' : 'teks',
                ])->all(),
            ])
            ->all();

        return view('courses.show', [
            'course' => $this->presentCourse($courseModel),
            'syllabus' => $syllabus,
        ]);
    }
}
