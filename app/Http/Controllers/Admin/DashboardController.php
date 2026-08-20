<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\User;
use App\Support\CoursePresentation;
use Illuminate\Support\Carbon;

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
            ],
            'registrationChart' => $this->registrationChartData(),
            'topCourses' => $this->topCourses(),
            'recentParticipants' => $this->recentParticipants(),
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

    private function recentParticipants()
    {
        return User::query()
            ->where('role', 'pelajar')
            ->withCount('enrollments')
            ->with(['enrollments' => fn ($query) => $query->latest()->with('course:id,title')->limit(1)])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'joined_at' => $user->created_at,
                'courses_enrolled' => $user->enrollments_count,
                'latest_course' => $user->enrollments->first()?->course?->title,
            ]);
    }
}
