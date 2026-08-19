<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Halaman kelola materi (modul & pelajaran) di panel admin — CRUD asli ke
 * tabel modules/lessons (Eloquent). Menggantikan versi sebelumnya yang
 * masih pakai data tiruan (App\Support\MockData::syllabusFor).
 */
class ModuleController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::orderBy('title')->get(['id', 'title']);
        $selectedCourseId = (int) $request->query('course', $courses->first()?->id ?? 0);
        $selectedCourse = $courses->firstWhere('id', $selectedCourseId) ?? $courses->first();

        $modules = $selectedCourse
            ? Module::where('course_id', $selectedCourse->id)->with('lessons')->orderBy('position')->get()
            : collect();

        return view('admin.modules.index', [
            'courses' => $courses,
            'selectedCourse' => $selectedCourse,
            'modules' => $modules->map(fn (Module $module) => $this->presentModule($module)),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => ['required', 'integer', Rule::exists('courses', 'id')],
            'title' => ['required', 'string', 'max:255'],
        ]);

        $position = Module::where('course_id', $validated['course_id'])->max('position') + 1;

        Module::create([...$validated, 'position' => $position]);

        return redirect()
            ->route('admin.modules.index', ['course' => $validated['course_id']])
            ->with('notice', 'Modul baru berhasil ditambahkan.');
    }

    public function update(Request $request, Module $module): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $module->update($validated);

        return redirect()
            ->route('admin.modules.index', ['course' => $module->course_id])
            ->with('notice', 'Perubahan modul berhasil disimpan.');
    }

    public function destroy(Module $module): RedirectResponse
    {
        $courseId = $module->course_id;
        $module->delete();

        return redirect()
            ->route('admin.modules.index', ['course' => $courseId])
            ->with('notice', 'Modul beserta pelajaran di dalamnya berhasil dihapus.');
    }

    public function storeLesson(Request $request, Module $module): RedirectResponse
    {
        $validated = $this->lessonValidated($request);
        $position = $module->lessons()->max('position') + 1;

        $module->lessons()->create([...$validated, 'position' => $position]);

        return redirect()
            ->route('admin.modules.index', ['course' => $module->course_id])
            ->with('notice', 'Pelajaran baru berhasil ditambahkan.');
    }

    public function updateLesson(Request $request, Module $module, Lesson $lesson): RedirectResponse
    {
        $lesson->update($this->lessonValidated($request));

        return redirect()
            ->route('admin.modules.index', ['course' => $module->course_id])
            ->with('notice', 'Perubahan pelajaran berhasil disimpan.');
    }

    public function destroyLesson(Module $module, Lesson $lesson): RedirectResponse
    {
        $lesson->delete();

        return redirect()
            ->route('admin.modules.index', ['course' => $module->course_id])
            ->with('notice', 'Pelajaran berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function lessonValidated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['teks', 'video'])],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'string', 'max:255'],
        ]);

        // Kolom 'type' bukan kolom asli (lihat CourseController::show) —
        // video/teks ditentukan dari ada tidaknya video_url, jadi field yang
        // tidak relevan dengan tipe yang dipilih sengaja dikosongkan.
        return [
            'title' => $data['title'],
            'content' => $data['type'] === 'teks' ? ($data['content'] ?? null) : null,
            'video_url' => $data['type'] === 'video' ? ($data['video_url'] ?? null) : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentModule(Module $module): array
    {
        return [
            'id' => $module->id,
            'title' => $module->title,
            'lessons' => $module->lessons->map(fn (Lesson $lesson) => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'type' => $lesson->video_url ? 'video' : 'teks',
                'content' => (string) $lesson->content,
                'video_url' => (string) $lesson->video_url,
            ])->all(),
        ];
    }
}
