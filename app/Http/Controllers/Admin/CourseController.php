<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Support\CoursePresentation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Halaman kelola kursus di panel admin — CRUD asli ke tabel courses
 * (Eloquent). Menggantikan versi sebelumnya yang masih pakai data tiruan
 * (App\Support\MockData); bentuk array yang dikirim ke view sengaja
 * dipertahankan sama supaya perubahan di sisi Blade minimal.
 */
class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::query()
            ->with('category')
            ->withCount(['modules', 'lessons', 'enrollments as students_count'])
            ->orderBy('title')
            ->get()
            ->map(fn (Course $course) => $this->present($course));

        return view('admin.courses.index', [
            'courses' => $courses,
            'categories' => Category::orderBy('name')->get(['id', 'name'])->toArray(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['slug'] = $this->uniqueSlug($validated['title']);

        Course::create($validated);

        return redirect()->route('admin.courses.index')->with('notice', 'Kursus baru berhasil ditambahkan.');
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $this->validated($request);

        // Slug cuma dibuat ulang kalau judul berubah, supaya URL kursus yang
        // sudah dibagikan/di-bookmark tidak tiba-tiba berubah tanpa alasan.
        if ($validated['title'] !== $course->title) {
            $validated['slug'] = $this->uniqueSlug($validated['title'], ignoreCourseId: $course->id);
        }

        $course->update($validated);

        return redirect()->route('admin.courses.index')->with('notice', 'Perubahan kursus berhasil disimpan.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('admin.courses.index')->with('notice', 'Kursus berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'level' => ['required', Rule::in(['Pemula', 'Menengah', 'Lanjutan', 'Semua Level'])],
            'description' => ['nullable', 'string'],
        ]);

        // Checkbox HTML tidak dikirim sama sekali kalau tidak dicentang,
        // jadi ambil lewat $request->boolean() bukan lewat rule 'boolean'.
        $validated['is_published'] = $request->boolean('is_published');

        return $validated;
    }

    private function uniqueSlug(string $title, ?int $ignoreCourseId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 2;

        while (
            Course::where('slug', $slug)
                ->when($ignoreCourseId, fn ($query) => $query->where('id', '!=', $ignoreCourseId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    /**
     * @return array<string, mixed>
     */
    private function present(Course $course): array
    {
        return [
            'id' => $course->id,
            'category_id' => $course->category_id,
            'category' => $course->category?->name,
            'title' => $course->title,
            'slug' => $course->slug,
            'description' => (string) $course->description,
            'level' => $course->level,
            'thumbnail_icon' => CoursePresentation::thumbnailIcon($course->category?->slug),
            'thumbnail_badge' => CoursePresentation::badgeClass($course->category?->slug),
            'modules_count' => $course->modules_count,
            'lessons_count' => $course->lessons_count,
            'students_count' => $course->students_count,
            'is_published' => $course->is_published,
        ];
    }
}
