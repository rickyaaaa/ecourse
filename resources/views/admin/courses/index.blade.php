@extends('layouts.admin')

@section('title', 'Kelola Kursus — Panel Admin')
@section('page-title', 'Kelola Kursus')

@section('content')
<div
    x-data="{
        courses: {{ Js::from($courses) }},
        search: '',
        categoryFilter: '',
        statusFilter: '',
        showModal: false,
        editingId: null,
        form: { title: '', category_id: '', level: 'Pemula', description: '', is_published: true },
        get filteredCourses() {
            return this.courses.filter(course => {
                const matchesSearch = this.search.trim() === ''
                    || course.title.toLowerCase().includes(this.search.trim().toLowerCase());
                const matchesCategory = this.categoryFilter === '' || course.category === this.categoryFilter;
                const matchesStatus = this.statusFilter === ''
                    || (this.statusFilter === 'published' && course.is_published)
                    || (this.statusFilter === 'draft' && ! course.is_published);
                return matchesSearch && matchesCategory && matchesStatus;
            });
        },
        openCreateModal() {
            this.editingId = null;
            this.form = { title: '', category_id: {{ Js::from($categories[0]['id'] ?? '') }}, level: 'Pemula', description: '', is_published: true };
            this.showModal = true;
        },
        openEditModal(course) {
            this.editingId = course.id;
            this.form = {
                title: course.title,
                category_id: course.category_id,
                level: course.level,
                description: course.description,
                is_published: course.is_published,
            };
            this.showModal = true;
        },
        deleteCourse(course) {
            if (! confirm(`Hapus kursus '${course.title}'? Modul, pelajaran, dan kuis di dalamnya ikut terhapus. Tindakan ini tidak bisa dibatalkan.`)) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/kursus/${course.id}`;

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
        @if ($errors->any())
            editingId = {{ old('_editing_id') ? (int) old('_editing_id') : 'null' }};
            form = {
                title: {{ Js::from(old('title', '')) }},
                category_id: {{ Js::from(old('category_id', '')) }},
                level: {{ Js::from(old('level', 'Pemula')) }},
                description: {{ Js::from(old('description', '')) }},
                is_published: {{ old('is_published') ? 'true' : 'false' }},
            };
            showModal = true;
        @endif
    "
>
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-3 mb-24">
        <div>
            <h5 class="fw-bold mb-1">Daftar Kursus</h5>
            <p class="text-secondary-light mb-0">Kelola kursus yang tampil di katalog publik.</p>
        </div>
        <button @click="openCreateModal()" class="btn btn-primary-600 radius-8 d-flex align-items-center gap-1 px-16 py-8">
            <i class="ri-add-line"></i> Tambah Kursus
        </button>
    </div>

    <div class="card radius-8 border mb-24">
        <div class="card-body p-24">
            <div class="row gy-3 mb-24">
                <div class="col-md-5">
                    <label class="visually-hidden">Cari kursus</label>
                    <div class="position-relative">
                        <input type="search" x-model="search" placeholder="Cari judul kursus..." class="form-control radius-8 ps-40">
                        <i class="ri-search-line position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <select x-model="categoryFilter" class="form-select radius-8">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category['name'] }}">{{ $category['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select x-model="statusFilter" class="form-select radius-8">
                        <option value="">Semua Status</option>
                        <option value="published">Diterbitkan</option>
                        <option value="draft">Draf</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive scroll-sm">
                <table class="table bordered-table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Kursus</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Level</th>
                            <th scope="col">Modul / Pelajaran</th>
                            <th scope="col">Peserta</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="course in filteredCourses" :key="course.id">
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="w-36-px h-36-px flex-shrink-0 d-flex justify-content-center align-items-center radius-8 bg-primary-50 text-lg" x-text="course.thumbnail_icon"></span>
                                        <span class="fw-medium text-secondary-light" x-text="course.title"></span>
                                    </div>
                                </td>
                                <td class="text-secondary-light" x-text="course.category"></td>
                                <td class="text-secondary-light" x-text="course.level"></td>
                                <td class="text-secondary-light">
                                    <span x-text="course.modules_count"></span> modul · <span x-text="course.lessons_count"></span> pelajaran
                                </td>
                                <td class="text-secondary-light" x-text="course.students_count.toLocaleString('id-ID')"></td>
                                <td>
                                    <span
                                        class="border px-12 py-2 radius-4 fw-medium text-sm"
                                        :class="course.is_published ? 'bg-success-focus text-success-600 border-success-main' : 'bg-neutral-200 text-neutral-600 border-neutral-400'"
                                        x-text="course.is_published ? 'Diterbitkan' : 'Draf'"
                                    ></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-8 justify-content-center">
                                        <button type="button" @click="openEditModal(course)" class="bg-success-focus bg-hover-success-200 text-success-600 fw-medium w-32-px h-32-px d-flex justify-content-center align-items-center rounded-circle" aria-label="Ubah kursus">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button type="button" @click="deleteCourse(course)" class="bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-32-px h-32-px d-flex justify-content-center align-items-center rounded-circle" aria-label="Hapus kursus">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredCourses.length === 0" x-cloak>
                            <td colspan="7" class="text-center text-secondary-light py-40">
                                Tidak ada kursus yang cocok dengan pencarian atau filter kamu.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal tambah/ubah kursus --}}
    {{--
        PENTING: div x-show/x-cloak di bawah ini SENGAJA tidak diberi class
        d-flex (atau class display-* Bootstrap lain). Bootstrap men-set
        display:...!important pada class-class itu, yang mengalahkan baik
        x-show milik Alpine maupun style="display:none" statis (inline
        style non-important kalah dari class !important, berapa pun
        specificity-nya) — akibatnya modal tidak akan pernah bisa
        disembunyikan lagi setelah Alpine melepas atribut x-cloak. Layout
        flex-center dipindah ke div pembungkus terpisah di dalamnya yang
        TIDAK dikontrol x-show, jadi tidak pernah bentrok.
    --}}
    <div x-show="showModal" x-cloak class="position-fixed top-0 start-0 w-100 h-100" style="display:none;z-index:1050;">
        <div @click="showModal = false" class="position-fixed top-0 start-0 w-100 h-100 bg-dark" style="opacity:.5;"></div>

        <div class="d-flex align-items-center justify-content-center w-100 h-100 p-16">
        <div class="position-relative bg-white radius-8 shadow w-100 p-24" style="max-width:560px;max-height:90vh;overflow-y:auto;">
            <h6 class="fw-bold mb-16" x-text="editingId === null ? 'Tambah Kursus' : 'Ubah Kursus'"></h6>

            <form
                method="POST"
                :action="editingId === null ? '{{ route('admin.courses.store') }}' : '{{ url('/admin/kursus') }}/' + editingId"
                class="d-flex flex-column gap-16"
            >
                @csrf
                <input type="hidden" name="_method" :value="editingId === null ? 'POST' : 'PUT'">
                <input type="hidden" name="_editing_id" :value="editingId">

                <div>
                    <label for="course_title" class="form-label fw-medium">Judul Kursus</label>
                    <input type="text" id="course_title" name="title" x-model="form.title" required class="form-control radius-8">
                    @error('title')
                        <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label for="course_category" class="form-label fw-medium">Kategori</label>
                        <select id="course_category" name="category_id" x-model="form.category_id" class="form-select radius-8">
                            @foreach ($categories as $category)
                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-6">
                        <label for="course_level" class="form-label fw-medium">Level</label>
                        <select id="course_level" name="level" x-model="form.level" class="form-select radius-8">
                            <option value="Pemula">Pemula</option>
                            <option value="Menengah">Menengah</option>
                            <option value="Lanjutan">Lanjutan</option>
                            <option value="Semua Level">Semua Level</option>
                        </select>
                        @error('level')
                            <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="course_description" class="form-label fw-medium">Deskripsi</label>
                    <textarea id="course_description" name="description" x-model="form.description" rows="3" class="form-control radius-8"></textarea>
                    @error('description')
                        <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-check d-flex align-items-center gap-2">
                    <input type="checkbox" id="course_is_published" name="is_published" value="1" x-model="form.is_published" class="form-check-input">
                    <label for="course_is_published" class="form-check-label text-secondary-light">Terbitkan kursus ini</label>
                </div>

                <div class="d-flex justify-content-end gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="btn btn-outline-secondary radius-8 px-16 py-8">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-8">Simpan</button>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>
@endsection
