@extends('layouts.admin')

@section('title', 'Kelola Materi — Panel Admin')
@section('page-title', 'Kelola Materi')

@section('content')
<div class="mb-6">
    <h2 class="text-lg font-semibold text-gray-900">Modul & Pelajaran</h2>
    <p class="mt-1 text-sm text-gray-500">Kelola modul dan pelajaran yang tampil di halaman detail kursus.</p>
</div>

<form method="GET" action="{{ route('admin.modules.index') }}" class="mb-6 sm:w-72">
    <label for="course" class="block text-sm font-medium text-gray-700">Pilih Kursus</label>
    <select
        id="course"
        name="course"
        x-data
        x-on:change="$el.form.requestSubmit()"
        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
    >
        @foreach ($courses as $course)
            <option value="{{ $course->id }}" @selected($selectedCourse && $selectedCourse->id === $course->id)>
                {{ $course->title }}
            </option>
        @endforeach
    </select>
    <noscript>
        <button type="submit" class="mt-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
            Tampilkan
        </button>
    </noscript>
</form>

@if (! $selectedCourse)
    <div class="rounded-lg border border-dashed border-gray-300 p-10 text-center text-gray-500">
        Belum ada kursus yang tersedia. Tambahkan kursus dulu di halaman Kelola Kursus.
    </div>
@else
    <div
        x-data="{
            modules: {{ Js::from($modules) }},
            expanded: {},
            showModuleModal: false,
            showLessonModal: false,
            editingModuleId: null,
            editingLessonId: null,
            activeModuleId: null,
            moduleForm: { title: '' },
            lessonForm: { title: '', type: 'teks', content: '', video_url: '' },
            toggleExpand(moduleId) {
                this.expanded[moduleId] = ! this.expanded[moduleId];
            },
            openCreateModule() {
                this.editingModuleId = null;
                this.moduleForm = { title: '' };
                this.showModuleModal = true;
            },
            openEditModule(module) {
                this.editingModuleId = module.id;
                this.moduleForm = { title: module.title };
                this.showModuleModal = true;
            },
            deleteModule(module) {
                if (! confirm(`Hapus modul \"${module.title}\" beserta seluruh pelajarannya? Tindakan ini tidak bisa dibatalkan.`)) return;
                this.submitDelete(`/admin/materi/${module.id}`);
            },
            openCreateLesson(module) {
                this.activeModuleId = module.id;
                this.editingLessonId = null;
                this.lessonForm = { title: '', type: 'teks', content: '', video_url: '' };
                this.showLessonModal = true;
            },
            openEditLesson(module, lesson) {
                this.activeModuleId = module.id;
                this.editingLessonId = lesson.id;
                this.lessonForm = { title: lesson.title, type: lesson.type, content: lesson.content, video_url: lesson.video_url };
                this.showLessonModal = true;
            },
            deleteLesson(module, lesson) {
                if (! confirm(`Hapus pelajaran \"${lesson.title}\"? Tindakan ini tidak bisa dibatalkan.`)) return;
                this.submitDelete(`/admin/materi/${module.id}/pelajaran/${lesson.id}`);
            },
            submitDelete(action) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = action;
                form.innerHTML = `
                    <input type=\"hidden\" name=\"_token\" value=\"${document.querySelector('meta[name=csrf-token]').content}\">
                    <input type=\"hidden\" name=\"_method\" value=\"DELETE\">
                `;
                document.body.appendChild(form);
                form.submit();
            },
        }"
        x-init="
            modules.forEach(m => expanded[m.id] = false);
            @if ($errors->any() && old('_form_type') === 'module')
                editingModuleId = {{ old('_editing_id') ? (int) old('_editing_id') : 'null' }};
                moduleForm = { title: {{ Js::from(old('title', '')) }} };
                showModuleModal = true;
            @elseif ($errors->any() && old('_form_type') === 'lesson')
                activeModuleId = {{ old('_active_module_id') ? (int) old('_active_module_id') : 'null' }};
                editingLessonId = {{ old('_editing_id') ? (int) old('_editing_id') : 'null' }};
                lessonForm = {
                    title: {{ Js::from(old('title', '')) }},
                    type: {{ Js::from(old('type', 'teks')) }},
                    content: {{ Js::from(old('content', '')) }},
                    video_url: {{ Js::from(old('video_url', '')) }},
                };
                if (activeModuleId !== null) { expanded[activeModuleId] = true; }
                showLessonModal = true;
            @endif
        "
    >
        <div class="mb-4 flex items-center justify-between">
            <h3 class="font-medium text-gray-900">{{ $selectedCourse->title }}</h3>
            <button
                @click="openCreateModule()"
                class="inline-flex items-center justify-center gap-1 rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500"
            >
                + Tambah Modul
            </button>
        </div>

        <div class="space-y-3">
            <template x-for="module in modules" :key="module.id">
                <div class="rounded-xl border border-gray-200 bg-white">
                    <div class="flex items-center justify-between px-4 py-3">
                        <button @click="toggleExpand(module.id)" class="flex flex-1 items-center gap-2 text-left">
                            <span x-text="expanded[module.id] ? '▾' : '▸'" class="text-gray-400"></span>
                            <span class="font-medium text-gray-900" x-text="module.title"></span>
                            <span class="text-xs text-gray-400" x-text="`(${module.lessons.length} pelajaran)`"></span>
                        </button>
                        <div class="flex shrink-0 items-center gap-3 text-sm">
                            <button @click="openCreateLesson(module)" class="font-medium text-indigo-600 hover:underline">+ Pelajaran</button>
                            <button @click="openEditModule(module)" class="font-medium text-indigo-600 hover:underline">Ubah</button>
                            <button @click="deleteModule(module)" class="font-medium text-red-600 hover:underline">Hapus</button>
                        </div>
                    </div>

                    <div x-show="expanded[module.id]" x-cloak class="divide-y divide-gray-100 border-t border-gray-100">
                        <template x-for="lesson in module.lessons" :key="lesson.id">
                            <div class="flex items-center justify-between px-4 py-2.5 pl-10 text-sm">
                                <div class="flex items-center gap-2">
                                    <span x-text="lesson.type === 'video' ? '🎬' : '📄'"></span>
                                    <span class="text-gray-700" x-text="lesson.title"></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button @click="openEditLesson(module, lesson)" class="font-medium text-indigo-600 hover:underline">Ubah</button>
                                    <button @click="deleteLesson(module, lesson)" class="font-medium text-red-600 hover:underline">Hapus</button>
                                </div>
                            </div>
                        </template>

                        <p x-show="module.lessons.length === 0" x-cloak class="px-4 py-3 pl-10 text-sm text-gray-400">
                            Belum ada pelajaran di modul ini.
                        </p>
                    </div>
                </div>
            </template>

            <p x-show="modules.length === 0" x-cloak class="rounded-lg border border-dashed border-gray-300 p-10 text-center text-gray-500">
                Belum ada modul untuk kursus ini.
            </p>
        </div>

        {{-- Modal tambah/ubah modul --}}
        <div x-show="showModuleModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div @click="showModuleModal = false" class="fixed inset-0 bg-gray-900/50"></div>

            <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900" x-text="editingModuleId === null ? 'Tambah Modul' : 'Ubah Modul'"></h3>

                <form
                    method="POST"
                    :action="editingModuleId === null ? '{{ route('admin.modules.store') }}' : '{{ url('/admin/materi') }}/' + editingModuleId"
                    class="mt-4 space-y-4"
                >
                    @csrf
                    <input type="hidden" name="_method" :value="editingModuleId === null ? 'POST' : 'PUT'">
                    <input type="hidden" name="_form_type" value="module">
                    <input type="hidden" name="_editing_id" :value="editingModuleId">
                    <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">

                    <div>
                        <label for="module_title" class="block text-sm font-medium text-gray-700">Judul Modul</label>
                        <input
                            type="text"
                            id="module_title"
                            name="title"
                            x-model="moduleForm.title"
                            required
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                        @error('title')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showModuleModal = false" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal tambah/ubah pelajaran --}}
        <div x-show="showLessonModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div @click="showLessonModal = false" class="fixed inset-0 bg-gray-900/50"></div>

            <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900" x-text="editingLessonId === null ? 'Tambah Pelajaran' : 'Ubah Pelajaran'"></h3>

                <form
                    method="POST"
                    :action="editingLessonId === null
                        ? '{{ url('/admin/materi') }}/' + activeModuleId + '/pelajaran'
                        : '{{ url('/admin/materi') }}/' + activeModuleId + '/pelajaran/' + editingLessonId"
                    class="mt-4 space-y-4"
                >
                    @csrf
                    <input type="hidden" name="_method" :value="editingLessonId === null ? 'POST' : 'PUT'">
                    <input type="hidden" name="_form_type" value="lesson">
                    <input type="hidden" name="_editing_id" :value="editingLessonId">
                    <input type="hidden" name="_active_module_id" :value="activeModuleId">

                    <div>
                        <label for="lesson_title" class="block text-sm font-medium text-gray-700">Judul Pelajaran</label>
                        <input
                            type="text"
                            id="lesson_title"
                            name="title"
                            x-model="lessonForm.title"
                            required
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                        @error('title')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="lesson_type" class="block text-sm font-medium text-gray-700">Tipe</label>
                        <select
                            id="lesson_type"
                            name="type"
                            x-model="lessonForm.type"
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                            <option value="teks">Teks</option>
                            <option value="video">Video</option>
                        </select>
                    </div>

                    <div x-show="lessonForm.type === 'teks'" x-cloak>
                        <label for="lesson_content" class="block text-sm font-medium text-gray-700">Konten (HTML sederhana)</label>
                        <textarea
                            id="lesson_content"
                            name="content"
                            x-model="lessonForm.content"
                            rows="4"
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        ></textarea>
                        @error('content')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="lessonForm.type === 'video'" x-cloak>
                        <label for="lesson_video_url" class="block text-sm font-medium text-gray-700">URL Video (YouTube/Vimeo)</label>
                        <input
                            type="text"
                            id="lesson_video_url"
                            name="video_url"
                            x-model="lessonForm.video_url"
                            placeholder="https://www.youtube.com/watch?v=..."
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                        @error('video_url')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showLessonModal = false" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
