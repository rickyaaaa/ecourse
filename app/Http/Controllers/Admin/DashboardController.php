<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\User;

/**
 * Dasbor ringkas panel admin: jumlah kursus, peserta, dan kuis. Route ini
 * dilindungi middleware 'auth' + 'admin' (lihat routes/web.php).
 */
class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'stats' => [
                'courses' => Course::count(),
                'published_courses' => Course::where('is_published', true)->count(),
                'students' => User::where('role', 'pelajar')->count(),
                'enrollments' => Enrollment::count(),
                'quizzes' => Quiz::count(),
            ],
        ]);
    }
}
