@extends('layouts.admin')

@section('title', 'Kelola Materi — Panel Admin')
@section('page-title', 'Kelola Materi')

@section('content')
<div class="mb-24">
    <h5 class="fw-bold mb-1">Modul & Pelajaran</h5>
    <p class="text-secondary-light mb-0">Kelola modul dan pelajaran yang tampil di halaman detail kursus.</p>
</div>

<div class="card radius-8 border mb-24">
    <div class="card-body p-20">
        <form method="GET" action="{{ route('admin.modules.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6 col-lg-4">
                <label for="course" class="form-label fw-medium d-flex align-items-center gap-1">
                    <i class="ri-book-open-line text-primary-600"></i> Pilih Kursus
                </label>
                <select id="course" name="course" x-data x-on:change="$el.form.requestSubmit()" class="form-select radius-8">
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected($selectedCourse && $selectedCourse->id === $course->id)>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <noscript>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-8">Tampilkan</button>
                </div>
            </noscript>
        </form>
    </div>
</div>

@if (! $selectedCourse)
    <div class="card radius-8 border">
        <div class="card-body text-center text-secondary-light py-40">
            Belum ada kursus yang tersedia. Tambahkan kursus dulu di halaman Kelola Kursus.
        </div>
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
                if (! confirm(`Hapus modul '${module.title}' beserta seluruh pelajarannya? Tindakan ini tidak bisa dibatalkan.`)) return;
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
                if (! confirm(`Hapus pelajaran '${lesson.title}'? Tindakan ini tidak bisa dibatalkan.`)) return;
                this.submitDelete(`/admin/materi/${module.id}/pelajaran/${lesson.id}`);
            },
            submitDelete(action) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = action;

                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = document.querySelector('meta[name=csrf-token]').content;
                form.appendChild(token);

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

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
        <div class="d-flex align-items-center justify-content-between mb-16">
            <h6 class="fw-bold mb-0">{{ $selectedCourse->title }}</h6>
            <button @click="openCreateModule()" class="btn btn-primary-600 radius-8 d-flex align-items-center gap-1 px-16 py-8">
                <i class="ri-add-line"></i> Tambah Modul
            </button>
        </div>

        <div class="d-flex flex-column gap-12">
            <template x-for="(module, index) in modules" :key="module.id">
                <div class="card radius-8 border" style="overflow:hidden;">
                    <div class="d-flex align-items-center justify-content-between px-16 py-12 bg-neutral-50 flex-wrap gap-3">
                        <button type="button" @click="toggleExpand(module.id)" class="btn btn-link text-decoration-none d-flex flex-grow-1 align-items-center gap-3 text-start p-0">
                            <span class="w-32-px h-32-px flex-shrink-0 d-flex justify-content-center align-items-center radius-8 bg-primary-50 text-primary-600 fw-bold text-sm" x-text="index + 1"></span>
                            <i class="text-secondary-light flex-shrink-0" :class="expanded[module.id] ? 'ri-arrow-down-s-line' : 'ri-arrow-right-s-line'"></i>
                            <span class="fw-semibold text-primary-light" x-text="module.title"></span>
                            <span class="bg-neutral-200 text-neutral-600 px-8 py-2 radius-4 text-sm flex-shrink-0" x-text="`${module.lessons.length} pelajaran`"></span>
                        </button>
                        <div class="d-flex flex-shrink-0 align-items-center gap-8">
                            <button type="button" @click="openCreateLesson(module)" class="btn btn-sm btn-outline-primary-600 radius-8 d-flex align-items-center gap-1 px-12 py-6">
                                <i class="ri-add-line"></i> Pelajaran
                            </button>
                            <button type="button" @click="openEditModule(module)" class="bg-success-focus bg-hover-success-200 text-success-600 fw-medium w-32-px h-32-px d-flex justify-content-center align-items-center rounded-circle" aria-label="Ubah modul">
                                <i class="ri-edit-line"></i>
                            </button>
                            <button type="button" @click="deleteModule(module)" class="bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-32-px h-32-px d-flex justify-content-center align-items-center rounded-circle" aria-label="Hapus modul">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>

                    <div x-show="expanded[module.id]" x-cloak class="border-top" style="border-left:3px solid var(--primary-100);margin-left:16px;">
                        <template x-for="lesson in module.lessons" :key="lesson.id">
                            <div class="d-flex align-items-center justify-content-between px-16 py-10 border-bottom flex-wrap gap-2" style="padding-left:24px !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <i :class="lesson.type === 'video' ? 'ri-play-circle-line text-info-main' : 'ri-file-text-line text-neutral-400'"></i>
                                    <span class="text-secondary-light" x-text="lesson.title"></span>
                                    <span
                                        class="px-8 py-2 radius-4 text-sm fw-medium"
                                        :class="lesson.type === 'video' ? 'bg-info-focus text-info-main' : 'bg-neutral-200 text-neutral-600'"
                                        x-text="lesson.type === 'video' ? 'Video' : 'Teks'"
                                    ></span>
                                </div>
                                <div class="d-flex align-items-center gap-8">
                                    <button type="button" @click="openEditLesson(module, lesson)" class="bg-success-focus bg-hover-success-200 text-success-600 w-28-px h-28-px d-flex justify-content-center align-items-center rounded-circle" aria-label="Ubah pelajaran">
                                        <i class="ri-edit-line text-sm"></i>
                                    </button>
                                    <button type="button" @click="deleteLesson(module, lesson)" class="bg-danger-focus bg-hover-danger-200 text-danger-600 w-28-px h-28-px d-flex justify-content-center align-items-center rounded-circle" aria-label="Hapus pelajaran">
                                        <i class="ri-delete-bin-line text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <p x-show="module.lessons.length === 0" x-cloak class="text-sm text-neutral-400 px-16 py-12 mb-0" style="padding-left:24px !important;">
                            Belum ada pelajaran di modul ini.
                        </p>
                    </div>
                </div>
            </template>

            <div x-show="modules.length === 0" x-cloak class="card radius-8 border">
                <div class="card-body text-center py-40">
                    <i class="ri-stack-line text-2xl text-neutral-300 d-block mb-8"></i>
                    <p class="text-secondary-light mb-0">Belum ada modul untuk kursus ini.</p>
                </div>
            </div>
        </div>

        {{--
            Modal tambah/ubah modul. Div x-show/x-cloak SENGAJA tidak
            diberi class d-flex (lihat catatan panjang di
            admin/courses/index.blade.php) — Bootstrap men-set
            display:...!important pada class display-*, mengalahkan x-show
            Alpine maupun style="display:none" statis. Layout flex-center
            dipindah ke div pembungkus terpisah yang tidak dikontrol x-show.
        --}}
        <div x-show="showModuleModal" x-cloak class="position-fixed top-0 start-0 w-100 h-100" style="display:none;z-index:1050;">
            <div @click="showModuleModal = false" class="position-fixed top-0 start-0 w-100 h-100 bg-dark" style="opacity:.5;"></div>

            <div class="d-flex align-items-center justify-content-center w-100 h-100 p-16">
            <div class="position-relative bg-white radius-8 shadow w-100 p-24" style="max-width:480px;">
                <h6 class="fw-bold mb-16" x-text="editingModuleId === null ? 'Tambah Modul' : 'Ubah Modul'"></h6>

                <form
                    method="POST"
                    :action="editingModuleId === null ? '{{ route('admin.modules.store') }}' : '{{ url('/admin/materi') }}/' + editingModuleId"
                    class="d-flex flex-column gap-16"
                >
                    @csrf
                    <input type="hidden" name="_method" :value="editingModuleId === null ? 'POST' : 'PUT'">
                    <input type="hidden" name="_form_type" value="module">
                    <input type="hidden" name="_editing_id" :value="editingModuleId">
                    <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">

                    <div>
                        <label for="module_title" class="form-label fw-medium">Judul Modul</label>
                        <input type="text" id="module_title" name="title" x-model="moduleForm.title" required class="form-control radius-8">
                        @error('title')
                            <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-3 pt-2">
                        <button type="button" @click="showModuleModal = false" class="btn btn-outline-secondary radius-8 px-16 py-8">Batal</button>
                        <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-8">Simpan</button>
                    </div>
                </form>
            </div>
            </div>
        </div>

        {{-- Modal tambah/ubah pelajaran (lihat catatan d-flex di modal modul di atas) --}}
        <div x-show="showLessonModal" x-cloak class="position-fixed top-0 start-0 w-100 h-100" style="display:none;z-index:1050;">
            <div @click="showLessonModal = false" class="position-fixed top-0 start-0 w-100 h-100 bg-dark" style="opacity:.5;"></div>

            <div class="d-flex align-items-center justify-content-center w-100 h-100 p-16">
            <div class="position-relative bg-white radius-8 shadow w-100 p-24" style="max-width:480px;max-height:90vh;overflow-y:auto;">
                <h6 class="fw-bold mb-16" x-text="editingLessonId === null ? 'Tambah Pelajaran' : 'Ubah Pelajaran'"></h6>

                <form
                    method="POST"
                    :action="editingLessonId === null
                        ? '{{ url('/admin/materi') }}/' + activeModuleId + '/pelajaran'
                        : '{{ url('/admin/materi') }}/' + activeModuleId + '/pelajaran/' + editingLessonId"
                    class="d-flex flex-column gap-16"
                >
                    @csrf
                    <input type="hidden" name="_method" :value="editingLessonId === null ? 'POST' : 'PUT'">
                    <input type="hidden" name="_form_type" value="lesson">
                    <input type="hidden" name="_editing_id" :value="editingLessonId">
                    <input type="hidden" name="_active_module_id" :value="activeModuleId">

                    <div>
                        <label for="lesson_title" class="form-label fw-medium">Judul Pelajaran</label>
                        <input type="text" id="lesson_title" name="title" x-model="lessonForm.title" required class="form-control radius-8">
                        @error('title')
                            <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="lesson_type" class="form-label fw-medium">Tipe</label>
                        <select id="lesson_type" name="type" x-model="lessonForm.type" class="form-select radius-8">
                            <option value="teks">Teks</option>
                            <option value="video">Video</option>
                        </select>
                    </div>

                    <div x-show="lessonForm.type === 'teks'" x-cloak>
                        <label for="lesson_content" class="form-label fw-medium">Konten (HTML sederhana)</label>
                        <textarea id="lesson_content" name="content" x-model="lessonForm.content" rows="4" class="form-control radius-8"></textarea>
                        @error('content')
                            <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="lessonForm.type === 'video'" x-cloak>
                        <label for="lesson_video_url" class="form-label fw-medium">URL Video (YouTube/Vimeo)</label>
                        <input type="text" id="lesson_video_url" name="video_url" x-model="lessonForm.video_url" placeholder="https://www.youtube.com/watch?v=..." class="form-control radius-8">
                        @error('video_url')
                            <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-3 pt-2">
                        <button type="button" @click="showLessonModal = false" class="btn btn-outline-secondary radius-8 px-16 py-8">Batal</button>
                        <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-8">Simpan</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
@endif
@endsection
