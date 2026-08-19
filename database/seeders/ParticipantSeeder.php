<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Support\MockData;
use Illuminate\Database\Seeder;

/**
 * Seed peserta (pelajar) beserta pendaftaran kursusnya, dari data tiruan
 * App\Support\MockData::participants(), supaya halaman "Kelola Peserta" di
 * panel admin punya data nyata untuk dikelola. Membutuhkan CourseSeeder
 * sudah dijalankan lebih dulu (perlu tabel courses terisi).
 */
class ParticipantSeeder extends Seeder
{
    public function run(): void
    {
        $courseIds = Course::orderBy('id')->pluck('id')->values();

        if ($courseIds->isEmpty()) {
            return;
        }

        foreach (MockData::participants() as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => 'pelajar',
                    'password' => 'password',
                    'email_verified_at' => now(),
                ],
            );
            $user->forceFill(['created_at' => $data['joined_at']])->save();

            $enrolledCourseIds = $courseIds->take($data['courses_enrolled'])->values();

            foreach ($enrolledCourseIds as $index => $courseId) {
                Enrollment::updateOrCreate(
                    ['user_id' => $user->id, 'course_id' => $courseId],
                    [
                        'status' => $index < $data['courses_completed'] ? 'completed' : 'ongoing',
                        'enrolled_at' => $data['joined_at'],
                    ],
                );
            }
        }
    }
}
