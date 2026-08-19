<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Daftarkan pengguna yang sedang masuk ke sebuah kursus ("Ikut Kursus").
     *
     * Autentikasi (Fase 3) belum dibangun, jadi pengecekan login dilakukan
     * manual: kalau belum login, arahkan kembali ke halaman detail kursus
     * dengan pesan supaya pelajar daftar/masuk dulu. Setelah halaman
     * login/register tersedia, redirect ini bisa diarahkan ke sana.
     */
    public function store(Request $request, string $course): RedirectResponse
    {
        $courseModel = Course::published()->where('slug', $course)->firstOrFail();

        $user = $request->user();

        if (! $user) {
            return redirect()
                ->route('courses.show', $courseModel->slug)
                ->with('notice', 'Silakan daftar atau masuk terlebih dahulu untuk mengikuti kursus ini.');
        }

        $enrollment = Enrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $courseModel->id],
            ['status' => 'ongoing', 'enrolled_at' => now()],
        );

        return redirect()
            ->route('courses.show', $courseModel->slug)
            ->with('notice', $enrollment->wasRecentlyCreated
                ? 'Kamu berhasil mendaftar kursus ini. Selamat belajar!'
                : 'Kamu sudah terdaftar di kursus ini.');
    }
}
