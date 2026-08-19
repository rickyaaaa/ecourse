<?php

namespace App\Support;

/**
 * Sumber data tiruan (stub) untuk halaman-halaman frontend sebelum backend
 * (migrasi, model, controller sungguhan) tersedia. Bentuk data mengikuti
 * skema database di PRD (tabel categories & courses) supaya gampang diganti
 * dengan query Eloquent asli nanti.
 */
class MockData
{
    /**
     * @return array<int, array{id: int, name: string, slug: string}>
     */
    public static function categories(): array
    {
        return [
            ['id' => 1, 'name' => 'Pengembangan Web', 'slug' => 'pengembangan-web'],
            ['id' => 2, 'name' => 'Data & AI', 'slug' => 'data-ai'],
            ['id' => 3, 'name' => 'Desain', 'slug' => 'desain'],
            ['id' => 4, 'name' => 'Bisnis & Karier', 'slug' => 'bisnis-karier'],
            ['id' => 5, 'name' => 'Bahasa', 'slug' => 'bahasa'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function courses(): array
    {
        return [
            [
                'id' => 1,
                'category_id' => 1,
                'category' => 'Pengembangan Web',
                'title' => 'Dasar-Dasar Laravel untuk Pemula',
                'slug' => 'dasar-dasar-laravel-untuk-pemula',
                'description' => 'Belajar membangun aplikasi web dengan Laravel dari nol: routing, controller, Eloquent, hingga Blade.',
                'thumbnail_color' => 'from-indigo-500 to-purple-500',
                'thumbnail_icon' => '🧑‍💻',
                'level' => 'Pemula',
                'modules_count' => 8,
                'lessons_count' => 42,
                'students_count' => 1284,
                'is_published' => true,
            ],
            [
                'id' => 2,
                'category_id' => 1,
                'category' => 'Pengembangan Web',
                'title' => 'Alpine.js: Interaktivitas Ringan tanpa Framework Berat',
                'slug' => 'alpinejs-interaktivitas-ringan',
                'description' => 'Menambahkan interaktivitas ke halaman Blade menggunakan Alpine.js tanpa perlu build step yang rumit.',
                'thumbnail_color' => 'from-sky-500 to-cyan-500',
                'thumbnail_icon' => '⚡',
                'level' => 'Menengah',
                'modules_count' => 5,
                'lessons_count' => 21,
                'students_count' => 512,
                'is_published' => true,
            ],
            [
                'id' => 3,
                'category_id' => 2,
                'category' => 'Data & AI',
                'title' => 'Pengantar Analisis Data dengan Python',
                'slug' => 'pengantar-analisis-data-python',
                'description' => 'Mengenal Pandas, Numpy, dan visualisasi data untuk mengolah dataset nyata.',
                'thumbnail_color' => 'from-emerald-500 to-teal-500',
                'thumbnail_icon' => '📊',
                'level' => 'Pemula',
                'modules_count' => 10,
                'lessons_count' => 55,
                'students_count' => 2039,
                'is_published' => true,
            ],
            [
                'id' => 4,
                'category_id' => 2,
                'category' => 'Data & AI',
                'title' => 'Machine Learning Dasar untuk Developer',
                'slug' => 'machine-learning-dasar-developer',
                'description' => 'Memahami konsep supervised & unsupervised learning serta penerapannya dengan scikit-learn.',
                'thumbnail_color' => 'from-rose-500 to-orange-500',
                'thumbnail_icon' => '🤖',
                'level' => 'Lanjutan',
                'modules_count' => 12,
                'lessons_count' => 60,
                'students_count' => 876,
                'is_published' => true,
            ],
            [
                'id' => 5,
                'category_id' => 3,
                'category' => 'Desain',
                'title' => 'UI/UX Design Fundamentals',
                'slug' => 'ui-ux-design-fundamentals',
                'description' => 'Prinsip dasar desain antarmuka dan pengalaman pengguna, dari wireframe sampai prototipe.',
                'thumbnail_color' => 'from-fuchsia-500 to-pink-500',
                'thumbnail_icon' => '🎨',
                'level' => 'Pemula',
                'modules_count' => 7,
                'lessons_count' => 34,
                'students_count' => 1567,
                'is_published' => true,
            ],
            [
                'id' => 6,
                'category_id' => 4,
                'category' => 'Bisnis & Karier',
                'title' => 'Strategi Membangun Personal Branding',
                'slug' => 'strategi-personal-branding',
                'description' => 'Membangun citra profesional di dunia kerja dan media sosial secara konsisten.',
                'thumbnail_color' => 'from-amber-500 to-yellow-500',
                'thumbnail_icon' => '📈',
                'level' => 'Semua Level',
                'modules_count' => 4,
                'lessons_count' => 16,
                'students_count' => 421,
                'is_published' => true,
            ],
            [
                'id' => 7,
                'category_id' => 5,
                'category' => 'Bahasa',
                'title' => 'Bahasa Inggris untuk Dunia Kerja',
                'slug' => 'bahasa-inggris-dunia-kerja',
                'description' => 'Meningkatkan kemampuan komunikasi profesional: email, presentasi, dan wawancara kerja.',
                'thumbnail_color' => 'from-blue-500 to-indigo-500',
                'thumbnail_icon' => '🗣️',
                'level' => 'Menengah',
                'modules_count' => 6,
                'lessons_count' => 30,
                'students_count' => 998,
                'is_published' => true,
            ],
            [
                'id' => 8,
                'category_id' => 3,
                'category' => 'Desain',
                'title' => 'Desain Grafis dengan Figma',
                'slug' => 'desain-grafis-dengan-figma',
                'description' => 'Membuat aset visual, poster, dan mockup produk menggunakan Figma dari dasar.',
                'thumbnail_color' => 'from-violet-500 to-purple-500',
                'thumbnail_icon' => '✏️',
                'level' => 'Pemula',
                'modules_count' => 6,
                'lessons_count' => 28,
                'students_count' => 1145,
                'is_published' => true,
            ],
        ];
    }

    /**
     * Keanggotaan kursus (enrollments) tiruan milik "pelajar" yang sedang
     * masuk, mengikuti struktur tabel enrollments di PRD (kolom status).
     * Dipakai untuk menentukan status tombol "Ikut Kursus" sebelum
     * autentikasi & backend tersedia.
     *
     * @return array<int, array{course_id: int, status: string, progress: int}>
     */
    public static function enrollments(): array
    {
        return [
            ['course_id' => 1, 'status' => 'ongoing', 'progress' => 65],
            ['course_id' => 3, 'status' => 'completed', 'progress' => 100],
        ];
    }

    /**
     * Daftar peserta (pelajar) tiruan untuk halaman kelola peserta di panel
     * admin, mengikuti struktur tabel users (kolom name, email, role) yang
     * dilengkapi ringkasan keikutsertaan kursus.
     *
     * @return array<int, array{id: int, name: string, email: string, joined_at: string, courses_enrolled: int, courses_completed: int, is_active: bool}>
     */
    public static function participants(): array
    {
        return [
            ['id' => 1, 'name' => 'Ayu Lestari', 'email' => 'ayu.lestari@example.com', 'joined_at' => '2026-01-12', 'courses_enrolled' => 3, 'courses_completed' => 2, 'is_active' => true],
            ['id' => 2, 'name' => 'Bagas Wicaksono', 'email' => 'bagas.wicaksono@example.com', 'joined_at' => '2026-02-03', 'courses_enrolled' => 1, 'courses_completed' => 1, 'is_active' => true],
            ['id' => 3, 'name' => 'Citra Ramadhani', 'email' => 'citra.ramadhani@example.com', 'joined_at' => '2026-02-18', 'courses_enrolled' => 4, 'courses_completed' => 0, 'is_active' => true],
            ['id' => 4, 'name' => 'Dimas Prasetyo', 'email' => 'dimas.prasetyo@example.com', 'joined_at' => '2026-03-05', 'courses_enrolled' => 2, 'courses_completed' => 2, 'is_active' => false],
            ['id' => 5, 'name' => 'Endah Kusuma', 'email' => 'endah.kusuma@example.com', 'joined_at' => '2026-03-21', 'courses_enrolled' => 1, 'courses_completed' => 0, 'is_active' => true],
            ['id' => 6, 'name' => 'Fajar Nugroho', 'email' => 'fajar.nugroho@example.com', 'joined_at' => '2026-04-02', 'courses_enrolled' => 5, 'courses_completed' => 3, 'is_active' => true],
            ['id' => 7, 'name' => 'Gita Permatasari', 'email' => 'gita.permatasari@example.com', 'joined_at' => '2026-04-19', 'courses_enrolled' => 2, 'courses_completed' => 1, 'is_active' => true],
            ['id' => 8, 'name' => 'Hendra Saputra', 'email' => 'hendra.saputra@example.com', 'joined_at' => '2026-05-08', 'courses_enrolled' => 1, 'courses_completed' => 0, 'is_active' => false],
            ['id' => 9, 'name' => 'Indah Wulandari', 'email' => 'indah.wulandari@example.com', 'joined_at' => '2026-05-27', 'courses_enrolled' => 3, 'courses_completed' => 3, 'is_active' => true],
            ['id' => 10, 'name' => 'Joko Santoso', 'email' => 'joko.santoso@example.com', 'joined_at' => '2026-06-14', 'courses_enrolled' => 2, 'courses_completed' => 0, 'is_active' => true],
        ];
    }

    /**
     * @return array{course_id: int, status: string, progress: int}|null
     */
    public static function enrollmentFor(int $courseId): ?array
    {
        foreach (self::enrollments() as $enrollment) {
            if ($enrollment['course_id'] === $courseId) {
                return $enrollment;
            }
        }

        return null;
    }

    /**
     * Cari satu kursus tiruan berdasarkan slug.
     */
    public static function findCourseBySlug(string $slug): ?array
    {
        foreach (self::courses() as $course) {
            if ($course['slug'] === $slug) {
                return $course;
            }
        }

        return null;
    }

    /**
     * Buat silabus tiruan (modul & pelajaran) untuk satu kursus, mengikuti
     * struktur tabel modules & lessons di PRD. Dibangkitkan otomatis dari
     * modules_count/lessons_count kursus supaya tiap kursus punya silabus
     * yang masuk akal tanpa perlu ditulis manual satu per satu.
     *
     * @return array<int, array{id: int, title: string, lessons: array<int, array{id: int, title: string, type: string, has_quiz: bool}>}>
     */
    public static function syllabusFor(int $courseId): array
    {
        $course = self::firstBy('id', $courseId);

        if (! $course) {
            return [];
        }

        $moduleTitles = [
            'Pengenalan & Orientasi', 'Konsep Dasar', 'Praktik Terpandu', 'Studi Kasus',
            'Teknik Lanjutan', 'Proyek Mini', 'Evaluasi & Latihan', 'Proyek Akhir',
            'Tips & Praktik Terbaik', 'Persiapan Portofolio',
        ];

        $lessonFragments = [
            'Pengantar', 'Latihan Praktik', 'Studi Kasus Nyata', 'Demo Langsung',
            'Rangkuman & Tips', 'Kesalahan Umum & Solusinya',
        ];

        $modulesCount = max(1, $course['modules_count']);
        $lessonsCount = max($modulesCount, $course['lessons_count']);
        $lessonsPerModule = intdiv($lessonsCount, $modulesCount);
        $remainder = $lessonsCount % $modulesCount;

        $modules = [];
        $lessonId = 1;

        for ($m = 1; $m <= $modulesCount; $m++) {
            $lessonsInModule = max(1, $lessonsPerModule + ($m <= $remainder ? 1 : 0));
            $lessons = [];

            for ($l = 1; $l <= $lessonsInModule; $l++) {
                $isLastInModule = $l === $lessonsInModule;

                $lessons[] = [
                    'id' => $lessonId,
                    'title' => "Pelajaran {$m}.{$l}: " . $lessonFragments[$lessonId % count($lessonFragments)],
                    'type' => $lessonId % 3 === 0 ? 'video' : 'teks',
                    'has_quiz' => $isLastInModule,
                ];

                $lessonId++;
            }

            $modules[] = [
                'id' => $m,
                'title' => 'Modul ' . $m . ': ' . ($moduleTitles[($m - 1) % count($moduleTitles)]),
                'lessons' => $lessons,
            ];
        }

        return $modules;
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function firstBy(string $key, mixed $value): ?array
    {
        foreach (self::courses() as $course) {
            if ($course[$key] === $value) {
                return $course;
            }
        }

        return null;
    }

    /**
     * Bank soal kuis generik (pertanyaan + opsi + pembahasan) yang dipakai
     * untuk membangkitkan kuis tiruan per modul. Mengikuti struktur tabel
     * quiz_questions & quiz_options di PRD.
     *
     * @return array<int, array{question_text: string, explanation: string, options: array<int, array{text: string, is_correct: bool}>}>
     */
    public static function quizQuestionBank(): array
    {
        return [
            [
                'question_text' => 'Apa tujuan utama mempelajari materi pada modul ini?',
                'explanation' => 'Tujuan utamanya adalah membangun pemahaman dasar sebelum masuk ke praktik yang lebih kompleks.',
                'options' => [
                    ['text' => 'Memahami konsep dasar sebelum praktik', 'is_correct' => true],
                    ['text' => 'Menghafal seluruh istilah tanpa praktik', 'is_correct' => false],
                    ['text' => 'Melewati materi karena dianggap tidak penting', 'is_correct' => false],
                    ['text' => 'Hanya menonton tanpa pernah mencoba', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => 'Manakah langkah paling tepat setelah mempelajari materi modul ini?',
                'explanation' => 'Mempraktikkan langsung apa yang dipelajari membantu memperkuat ingatan dan pemahaman.',
                'options' => [
                    ['text' => 'Mempraktikkan langsung apa yang sudah dipelajari', 'is_correct' => true],
                    ['text' => 'Melupakannya dan langsung lanjut ke topik lain', 'is_correct' => false],
                    ['text' => 'Menunggu tanpa melakukan apa pun', 'is_correct' => false],
                    ['text' => 'Hanya membaca ulang tanpa pernah mencoba', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => 'Apa yang sebaiknya dilakukan kalau masih bingung dengan materi modul ini?',
                'explanation' => 'Mengulang materi atau mencari referensi tambahan adalah langkah wajar dan dianjurkan saat masih bingung.',
                'options' => [
                    ['text' => 'Mengulang materi atau mencari referensi tambahan', 'is_correct' => true],
                    ['text' => 'Langsung menyerah sepenuhnya', 'is_correct' => false],
                    ['text' => 'Mengabaikan kebingungan tersebut', 'is_correct' => false],
                    ['text' => 'Menandai modul selesai begitu saja', 'is_correct' => false],
                ],
            ],
        ];
    }

    /**
     * Bangkitkan kuis tiruan untuk satu modul, deterministik berdasarkan
     * id modul supaya soal & pilihan jawabannya stabil (id sintetis sama
     * tiap kali dipanggil untuk modul yang sama).
     *
     * @return array{module_id: int, title: string, description: string, passing_score: int, questions: array<int, array<string, mixed>>}
     */
    public static function quizForModule(int $moduleId, string $moduleTitle): array
    {
        $bank = self::quizQuestionBank();
        $questionCount = count($bank);
        $baseScore = intdiv(100, $questionCount);
        $remainder = 100 % $questionCount;

        $questions = [];

        foreach ($bank as $index => $template) {
            $questionId = ($moduleId * 100) + $index + 1;

            $options = [];

            foreach (array_values($template['options']) as $optionIndex => $option) {
                $options[] = [
                    'id' => ($questionId * 10) + $optionIndex + 1,
                    'option_text' => $option['text'],
                    'is_correct' => $option['is_correct'],
                ];
            }

            $questions[] = [
                'id' => $questionId,
                'question_text' => $template['question_text'],
                // Soal terakhir menampung sisa pembagian supaya total pas 100.
                'score' => $baseScore + ($index === $questionCount - 1 ? $remainder : 0),
                'explanation' => $template['explanation'],
                'options' => $options,
            ];
        }

        return [
            'module_id' => $moduleId,
            'title' => "Kuis: {$moduleTitle}",
            'description' => 'Uji pemahamanmu terhadap materi yang baru saja kamu pelajari di modul ini.',
            'passing_score' => 70,
            'questions' => $questions,
        ];
    }
}
