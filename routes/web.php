<?php

use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ModuleController as AdminModuleController;
use App\Http\Controllers\Admin\ParticipantController as AdminParticipantController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CourseController::class, 'index'])->name('courses.index');

// Lupa-kata-sandi/atur-kata-sandi masih placeholder sampai task backend
// masing-masing dikerjakan; pendaftaran & login sudah sungguhan.
// middleware 'guest' supaya pengguna yang sudah masuk tidak balik lagi ke
// halaman masuk/daftar dkk.
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->name('login.store');
    Route::get('/daftar', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->name('register.store');
    Route::get('/lupa-kata-sandi', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/lupa-kata-sandi', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-kata-sandi/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-kata-sandi', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dasbor', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/riwayat-kuis', [QuizController::class, 'history'])->name('quizzes.history');
    Route::get('/kursus-saya', [DashboardController::class, 'enrolledCourses'])->name('dashboard.enrolledCourses');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/kata-sandi', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});
Route::get('/kursus/{course}', [CourseController::class, 'show'])->name('courses.show');
Route::post('/kursus/{course}/ikut', [EnrollmentController::class, 'store'])->name('enrollments.store');
Route::get('/kursus/{course}/pelajaran/{lesson}', [LessonController::class, 'show'])
    ->where('lesson', '[0-9]+')
    ->name('lessons.show');
Route::get('/kursus/{course}/pelajaran/{lesson}/berkas', [LessonController::class, 'download'])
    ->where('lesson', '[0-9]+')
    ->name('lessons.download');
Route::post('/kursus/{course}/pelajaran/{lesson}/selesai', [LessonController::class, 'toggleComplete'])
    ->where('lesson', '[0-9]+')
    ->name('lessons.toggleComplete');
Route::get('/kursus/{course}/modul/{module}/kuis', [QuizController::class, 'show'])
    ->where('module', '[0-9]+')
    ->name('quizzes.show');
Route::post('/kursus/{course}/modul/{module}/kuis/kirim', [QuizController::class, 'submit'])
    ->where('module', '[0-9]+')
    ->name('quizzes.submit');

// Panel admin: hanya untuk pengguna dengan role 'admin' (lihat middleware
// 'admin' di bootstrap/app.php). Untuk aksi per-baris yang butuh aturan
// lebih spesifik (mis. admin tidak boleh menonaktifkan/menghapus admin
// lain), lihat App\Policies\UserPolicy yang dipakai di ParticipantController.
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/kursus', [AdminCourseController::class, 'index'])->name('courses.index');
    Route::post('/kursus', [AdminCourseController::class, 'store'])->name('courses.store');
    Route::put('/kursus/{course}', [AdminCourseController::class, 'update'])->name('courses.update');
    Route::delete('/kursus/{course}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');

    Route::get('/materi', [AdminModuleController::class, 'index'])->name('modules.index');
    Route::post('/materi', [AdminModuleController::class, 'store'])->name('modules.store');

    Route::scopeBindings()->group(function () {
        Route::put('/materi/{module}', [AdminModuleController::class, 'update'])->name('modules.update');
        Route::delete('/materi/{module}', [AdminModuleController::class, 'destroy'])->name('modules.destroy');
        Route::post('/materi/{module}/pelajaran', [AdminModuleController::class, 'storeLesson'])->name('modules.lessons.store');
        Route::put('/materi/{module}/pelajaran/{lesson}', [AdminModuleController::class, 'updateLesson'])->name('modules.lessons.update');
        Route::delete('/materi/{module}/pelajaran/{lesson}', [AdminModuleController::class, 'destroyLesson'])->name('modules.lessons.destroy');
    });

    Route::get('/kuis', [AdminQuizController::class, 'index'])->name('quizzes.index');
    Route::post('/kuis', [AdminQuizController::class, 'store'])->name('quizzes.store');

    Route::scopeBindings()->group(function () {
        Route::put('/kuis/{quiz}', [AdminQuizController::class, 'update'])->name('quizzes.update');
        Route::post('/kuis/{quiz}/soal', [AdminQuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
        Route::put('/kuis/{quiz}/soal/{question}', [AdminQuizController::class, 'updateQuestion'])->name('quizzes.questions.update');
        Route::delete('/kuis/{quiz}/soal/{question}', [AdminQuizController::class, 'destroyQuestion'])->name('quizzes.questions.destroy');
    });

    Route::get('/peserta', [AdminParticipantController::class, 'index'])->name('participants.index');
    Route::post('/peserta', [AdminParticipantController::class, 'store'])->name('participants.store');
    Route::put('/peserta/{participant}/status', [AdminParticipantController::class, 'toggleStatus'])->name('participants.toggleStatus');
    Route::delete('/peserta/{participant}', [AdminParticipantController::class, 'destroy'])->name('participants.destroy');
});
