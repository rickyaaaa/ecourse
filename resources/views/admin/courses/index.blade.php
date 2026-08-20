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
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Daftar Kursus</h2>
            <p class="mt-1 text-sm text-gray-500">Kelola kursus yang tampil di katalog publik.</p>
        </div>
        <button
            @click="openCreateModal()"
            class="inline-flex items-center justify-center gap-1 rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500"
        >
            + Tambah Kursus
        </button>
    </div>

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <label class="relative flex-1">
            <span class="sr-only">Cari kursus</span>
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">🔎</span>
            <input
                type="search"
                x-model="search"
                placeholder="Cari judul kursus..."
                class="w-full rounded-md border border-gray-300 py-2 pl-9 pr-4 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
        </label>

        <select
            x-model="categoryFilter"
            class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:w-52"
        >
            <option value="">Semua Kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category['name'] }}">{{ $category['name'] }}</option>
            @endforeach
        </select>

        <select
            x-model="statusFilter"
            class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:w-44"
        >
            <option value="">Semua Status</option>
            <option value="published">Diterbitkan</option>
            <option value="draft">Draf</option>
        </select>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                    <th class="px-4 py-3">Kursus</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Level</th>
                    <th class="px-4 py-3">Modul / Pelajaran</th>
                    <th class="px-4 py-3">Peserta</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-for="course in filteredCourses" :key="course.id">
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br text-base"
                                    :class="course.thumbnail_color"
                                    x-text="course.thumbnail_icon"
                                ></span>
                                <span class="font-medium text-gray-900" x-text="course.title"></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600" x-text="course.category"></td>
                        <td class="px-4 py-3 text-gray-600" x-text="course.level"></td>
                        <td class="px-4 py-3 text-gray-600">
                            <span x-text="course.modules_count"></span> modul ·
                            <span x-text="course.lessons_count"></span> pelajaran
                        </td>
                        <td class="px-4 py-3 text-gray-600" x-text="course.students_count.toLocaleString('id-ID')"></td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="course.is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                x-text="course.is_published ? 'Diterbitkan' : 'Draf'"
                            ></span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button @click="openEditModal(course)" class="font-medium text-indigo-600 hover:underline">Ubah</button>
                            <span class="mx-1 text-gray-300">|</span>
                            <button @click="deleteCourse(course)" class="font-medium text-red-600 hover:underline">Hapus</button>
                        </td>
                    </tr>
                </template>

                <tr x-show="filteredCourses.length === 0" x-cloak>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                        Tidak ada kursus yang cocok dengan pencarian atau filter kamu.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Modal tambah/ubah kursus --}}
    <div
        x-show="showModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
    >
        <div @click="showModal = false" class="fixed inset-0 bg-gray-900/50"></div>

        <div class="relative w-full max-w-lg rounded-xl bg-white p-6 shadow-lg">
            <h3 class="text-lg font-semibold text-gray-900" x-text="editingId === null ? 'Tambah Kursus' : 'Ubah Kursus'"></h3>

            <form
                method="POST"
                :action="editingId === null ? '{{ route('admin.courses.store') }}' : '{{ url('/admin/kursus') }}/' + editingId"
                class="mt-4 space-y-4"
            >
                @csrf
                <input type="hidden" name="_method" :value="editingId === null ? 'POST' : 'PUT'">
                <input type="hidden" name="_editing_id" :value="editingId">

                <div>
                    <label for="course_title" class="block text-sm font-medium text-gray-700">Judul Kursus</label>
                    <input
                        type="text"
                        id="course_title"
                        name="title"
                        x-model="form.title"
                        required
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    >
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="course_category" class="block text-sm font-medium text-gray-700">Kategori</label>
                        <select
                            id="course_category"
                            name="category_id"
                            x-model="form.category_id"
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                            @foreach ($categories as $category)
                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="course_level" class="block text-sm font-medium text-gray-700">Level</label>
                        <select
                            id="course_level"
                            name="level"
                            x-model="form.level"
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                            <option value="Pemula">Pemula</option>
                            <option value="Menengah">Menengah</option>
                            <option value="Lanjutan">Lanjutan</option>
                            <option value="Semua Level">Semua Level</option>
                        </select>
                        @error('level')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="course_description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea
                        id="course_description"
                        name="description"
                        x-model="form.description"
                        rows="3"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    ></textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_published" value="1" x-model="form.is_published" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Terbitkan kursus ini
                </label>

                <div class="flex justify-end gap-3 pt-2">
                    <button
                        type="button"
                        @click="showModal = false"
                        class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
