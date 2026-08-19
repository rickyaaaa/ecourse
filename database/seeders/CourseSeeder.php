<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Support\MockData;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Seed the courses table beserta silabusnya (modules & lessons).
     *
     * Sumbernya App\Support\MockData supaya data di database sama persis
     * dengan data tiruan yang dipakai halaman frontend sebelumnya.
     * Membutuhkan CategorySeeder sudah dijalankan lebih dulu.
     */
    public function run(): void
    {
        $mockCategoriesById = collect(MockData::categories())->keyBy('id');

        foreach (MockData::courses() as $courseData) {
            $categorySlug = $mockCategoriesById[$courseData['category_id']]['slug'] ?? null;
            $category = $categorySlug ? Category::where('slug', $categorySlug)->first() : null;

            if (! $category) {
                continue;
            }

            $course = Course::updateOrCreate(
                ['slug' => $courseData['slug']],
                [
                    'category_id' => $category->id,
                    'title' => $courseData['title'],
                    'description' => $courseData['description'],
                    'level' => $courseData['level'],
                    'is_published' => $courseData['is_published'],
                ],
            );

            $this->seedSyllabus($course, $courseData['id']);
        }
    }

    /**
     * Buat modul & pelajaran untuk satu kursus dari silabus tiruan
     * (App\Support\MockData::syllabusFor), supaya halaman detail punya
     * konten nyata untuk ditampilkan.
     */
    private function seedSyllabus(Course $course, int $mockCourseId): void
    {
        foreach (MockData::syllabusFor($mockCourseId) as $position => $moduleData) {
            $module = Module::updateOrCreate(
                ['course_id' => $course->id, 'title' => $moduleData['title']],
                ['position' => $position + 1],
            );

            $lastLessonPosition = count($moduleData['lessons']) - 1;

            foreach ($moduleData['lessons'] as $lessonPosition => $lessonData) {
                Lesson::updateOrCreate(
                    ['module_id' => $module->id, 'title' => $lessonData['title']],
                    [
                        'content' => $lessonData['type'] === 'teks'
                            ? $this->sampleContent($course->title, $lessonData['title'])
                            : null,
                        // Big Buck Bunny (Blender Foundation, CC BY 3.0) — video tiruan yang benar-benar bisa diputar.
                        'video_url' => $lessonData['type'] === 'video' ? 'https://www.youtube.com/watch?v=aqz-KE-bpKQ' : null,
                        // Pelajaran terakhir tiap modul dapat lampiran ringkasan (dummy).
                        'file_path' => $lessonPosition === $lastLessonPosition ? 'lessons/materi-pendukung.txt' : null,
                        'position' => $lessonPosition + 1,
                    ],
                );
            }
        }
    }

    /**
     * Konten materi tiruan berbentuk HTML terstruktur (heading, paragraf,
     * daftar) supaya tampilan "Baca materi" bisa diuji dengan wajar — nanti
     * digantikan hasil editor materi sungguhan di panel admin (Filament).
     */
    private function sampleContent(string $courseTitle, string $lessonTitle): string
    {
        return <<<HTML
        <p>Pada pelajaran <strong>{$lessonTitle}</strong> ini, kamu akan mempelajari salah satu bagian penting dari kursus <em>{$courseTitle}</em>. Materi disusun agar bisa langsung dipraktikkan, bukan sekadar dihafal.</p>

        <h2>Yang akan kamu pelajari</h2>
        <ul>
            <li>Konsep inti dan istilah penting yang sering dipakai di topik ini.</li>
            <li>Langkah-langkah praktis yang bisa langsung kamu coba sendiri.</li>
            <li>Kesalahan umum yang sering terjadi dan cara menghindarinya.</li>
        </ul>

        <h2>Ringkasan</h2>
        <p>Setelah menyelesaikan pelajaran ini, coba ulangi langkah-langkahnya sendiri agar pemahamanmu makin kuat. Kalau sudah yakin, lanjutkan ke pelajaran berikutnya.</p>
        HTML;
    }
}
