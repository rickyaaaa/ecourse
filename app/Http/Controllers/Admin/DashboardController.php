<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Support\CoursePresentation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Dasbor panel admin: ringkasan + tren dari data sungguhan (bukan angka
 * pendapatan/harga fiktif — platform ini tidak punya sistem pembayaran).
 * Route ini dilindungi middleware 'auth' + 'admin' (lihat routes/web.php).
 */
class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $weekAgo = $now->copy()->subDays(7);
        $twoWeeksAgo = $now->copy()->subDays(14);

        $studentsThisWeek = User::where('role', 'pelajar')->where('created_at', '>=', $weekAgo)->count();
        $studentsLastWeek = User::where('role', 'pelajar')
            ->whereBetween('created_at', [$twoWeeksAgo, $weekAgo])
            ->count();

        $enrollmentsThisWeek = Enrollment::where('created_at', '>=', $weekAgo)->count();
        $enrollmentsLastWeek = Enrollment::whereBetween('created_at', [$twoWeeksAgo, $weekAgo])->count();

        return view('admin.dashboard', [
            'stats' => [
                'courses' => Course::count(),
                'published_courses' => Course::where('is_published', true)->count(),
                'students' => User::where('role', 'pelajar')->count(),
                'students_trend' => $this->percentTrend($studentsLastWeek, $studentsThisWeek),
                'enrollments' => Enrollment::count(),
                'enrollments_trend' => $this->percentTrend($enrollmentsLastWeek, $enrollmentsThisWeek),
                'completed_enrollments' => Enrollment::where('status', 'completed')->count(),
                'quizzes' => Quiz::count(),
                'average_progress' => $this->averageProgress(),
            ],
            'registrationChart' => $this->registrationChartData(),
            'topCourses' => $this->topCourses(),
            'recentEnrollments' => $this->recentEnrollments(),
            'recentActivity' => $this->recentActivity(),
        ]);
    }

    /**
     * Perubahan persentase dari $previous ke $current, dibulatkan 1 desimal.
     * null kalau $previous nol (tidak ada basis pembanding yang wajar).
     */
    private function percentTrend(int $previous, int $current): ?array
    {
        if ($previous === 0) {
            return $current > 0 ? ['direction' => 'up', 'value' => null] : null;
        }

        $change = round((($current - $previous) / $previous) * 100, 1);

        return [
            'direction' => $change >= 0 ? 'up' : 'down',
            'value' => abs($change),
        ];
    }

    /**
     * Rata-rata progres semua pendaftaran (persentase pelajaran selesai
     * dibanding total pelajaran di kursus masing-masing). Kursus tanpa
     * pelajaran diabaikan (tidak ada progres yang bisa dihitung).
     */
    private function averageProgress(): ?float
    {
        $lessonCounts = Course::withCount('lessons')->get()->pluck('lessons_count', 'id');

        $completedCounts = DB::table('lesson_progress')
            ->join('lessons', 'lessons.id', '=', 'lesson_progress.lesson_id')
            ->join('modules', 'modules.id', '=', 'lessons.module_id')
            ->where('lesson_progress.is_completed', true)
            ->selectRaw('lesson_progress.user_id, modules.course_id, count(*) as completed')
            ->groupBy('lesson_progress.user_id', 'modules.course_id')
            ->get()
            ->keyBy(fn ($row) => $row->user_id.'-'.$row->course_id);

        $percentages = Enrollment::all()
            ->map(function (Enrollment $enrollment) use ($lessonCounts, $completedCounts) {
                $total = $lessonCounts->get($enrollment->course_id, 0);

                if ($total === 0) {
                    return null;
                }

                $completed = $completedCounts->get($enrollment->user_id.'-'.$enrollment->course_id)->completed ?? 0;

                return min(100, ($completed / $total) * 100);
            })
            ->filter(fn ($value) => $value !== null);

        return $percentages->isEmpty() ? null : round($percentages->avg(), 1);
    }

    /**
     * Pendaftaran peserta baru per hari, 14 hari terakhir — dipakai grafik
     * garis di dasbor. Hari tanpa pendaftaran tetap muncul dengan nilai 0
     * supaya sumbu waktunya tidak bolong.
     */
    private function registrationChartData(): array
    {
        $start = Carbon::now()->subDays(13)->startOfDay();

        $counts = User::where('role', 'pelajar')
            ->where('created_at', '>=', $start)
            ->get()
            ->groupBy(fn (User $user) => $user->created_at->toDateString());

        $labels = [];
        $values = [];

        for ($day = $start->copy(); $day->lte(Carbon::now()); $day->addDay()) {
            $labels[] = $day->translatedFormat('d M');
            $values[] = $counts->get($day->toDateString(), collect())->count();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function topCourses()
    {
        return Course::query()
            ->withCount(['enrollments', 'modules'])
            ->with('category:id,slug,name')
            ->orderByDesc('enrollments_count')
            ->limit(5)
            ->get()
            ->map(fn (Course $course) => [
                'id' => $course->id,
                'title' => $course->title,
                'category' => $course->category?->name,
                'is_published' => $course->is_published,
                'modules_count' => $course->modules_count,
                'lessons_count' => $course->lessons()->count(),
                'students_count' => $course->enrollments_count,
                'thumbnail_color' => CoursePresentation::thumbnailColor($course->category?->slug),
                'thumbnail_icon' => CoursePresentation::thumbnailIcon($course->category?->slug),
            ]);
    }

    /**
     * 8 pendaftaran kursus (enrollment) terbaru, untuk tabel "Enrollment
     * Terbaru" di dasbor.
     */
    private function recentEnrollments()
    {
        return Enrollment::query()
            ->with(['user:id,name,email', 'course:id,title'])
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (Enrollment $enrollment) => [
                'participant' => $enrollment->user?->name,
                'course' => $enrollment->course?->title,
                'date' => $enrollment->created_at,
                'status' => $enrollment->status,
            ]);
    }

    /**
     * Gabungan 3 jenis aktivitas terbaru (pendaftaran akun, pendaftaran
     * kursus, percobaan kuis) diurutkan waktu terbaru — bukan tabel/model
     * "activity log" tersendiri, cuma dirangkai dari data yang sudah ada.
     */
    private function recentActivity()
    {
        $registrations = User::where('role', 'pelajar')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (User $user) => [
                'type' => 'registration',
                'label' => "{$user->name} mendaftar sebagai peserta baru",
                'at' => $user->created_at,
            ]);

        $enrollments = Enrollment::with(['user:id,name', 'course:id,title'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Enrollment $enrollment) => [
                'type' => 'enrollment',
                'label' => "{$enrollment->user?->name} mengikuti kursus {$enrollment->course?->title}",
                'at' => $enrollment->created_at,
            ]);

        $quizAttempts = QuizAttempt::with(['user:id,name', 'quiz:id,title'])
            ->whereNotNull('finished_at')
            ->latest('finished_at')
            ->limit(5)
            ->get()
            ->map(fn (QuizAttempt $attempt) => [
                'type' => 'quiz',
                'label' => "{$attempt->user?->name} menyelesaikan {$attempt->quiz?->title} (skor {$attempt->score})",
                'at' => $attempt->finished_at,
            ]);

        return $registrations
            ->concat($enrollments)
            ->concat($quizAttempts)
            ->sortByDesc('at')
            ->take(8)
            ->values();
    }
}
