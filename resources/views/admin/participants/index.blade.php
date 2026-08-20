@extends('layouts.admin')

@section('title', 'Kelola Peserta — Panel Admin')
@section('page-title', 'Kelola Peserta')

@section('content')
<div
    x-data="{
        participants: {{ Js::from($participants) }},
        search: '',
        statusFilter: '',
        showModal: false,
        form: { name: '', email: '', password: '', password_confirmation: '' },
        get filteredParticipants() {
            return this.participants.filter(p => {
                const q = this.search.trim().toLowerCase();
                const matchesSearch = q === '' || p.name.toLowerCase().includes(q) || p.email.toLowerCase().includes(q);
                const matchesStatus = this.statusFilter === ''
                    || (this.statusFilter === 'active' && p.is_active)
                    || (this.statusFilter === 'inactive' && ! p.is_active);
                return matchesSearch && matchesStatus;
            });
        },
        openCreateModal() {
            this.form = { name: '', email: '', password: '', password_confirmation: '' };
            this.showModal = true;
        },
        submitAction(action, method) {
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
            methodField.value = method;
            form.appendChild(methodField);

            document.body.appendChild(form);
            form.submit();
        },
        toggleStatus(participant) {
            this.submitAction(`/admin/peserta/${participant.id}/status`, 'PUT');
        },
        deleteParticipant(participant) {
            if (! confirm(`Hapus peserta '${participant.name}'? Tindakan ini tidak bisa dibatalkan.`)) return;
            this.submitAction(`/admin/peserta/${participant.id}`, 'DELETE');
        },
    }"
    x-init="@if ($errors->any()) showModal = true; @endif"
>
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-3 mb-24">
        <div>
            <h5 class="fw-bold mb-1">Daftar Peserta</h5>
            <p class="text-secondary-light mb-0">Kelola akun pelajar yang terdaftar di platform.</p>
        </div>
        <button @click="openCreateModal()" class="btn btn-primary-600 radius-8 d-flex align-items-center gap-1 px-16 py-8">
            <i class="ri-add-line"></i> Tambah Peserta
        </button>
    </div>

    <div class="card radius-8 border">
        <div class="card-body p-24">
            <div class="row gy-3 mb-24">
                <div class="col-md-8">
                    <div class="position-relative">
                        <input type="search" x-model="search" placeholder="Cari nama atau email peserta..." class="form-control radius-8 ps-40">
                        <i class="ri-search-line position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <select x-model="statusFilter" class="form-select radius-8">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive scroll-sm">
                <table class="table bordered-table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col">Email</th>
                            <th scope="col">Bergabung</th>
                            <th scope="col">Kursus Diikuti</th>
                            <th scope="col">Kursus Selesai</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="participant in filteredParticipants" :key="participant.id">
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="w-32-px h-32-px flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle bg-primary-600 text-white fw-semibold text-sm" x-text="participant.name.charAt(0).toUpperCase()"></span>
                                        <span class="fw-medium text-secondary-light" x-text="participant.name"></span>
                                    </div>
                                </td>
                                <td class="text-secondary-light" x-text="participant.email"></td>
                                <td class="text-secondary-light" x-text="participant.joined_at"></td>
                                <td class="text-secondary-light" x-text="participant.courses_enrolled"></td>
                                <td class="text-secondary-light" x-text="participant.courses_completed"></td>
                                <td>
                                    <span
                                        class="border px-12 py-2 radius-4 fw-medium text-sm"
                                        :class="participant.is_active ? 'bg-success-focus text-success-600 border-success-main' : 'bg-neutral-200 text-neutral-600 border-neutral-400'"
                                        x-text="participant.is_active ? 'Aktif' : 'Nonaktif'"
                                    ></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-8 justify-content-center">
                                        <button
                                            type="button"
                                            @click="toggleStatus(participant)"
                                            class="bg-hover-warning-200 fw-medium w-32-px h-32-px d-flex justify-content-center align-items-center rounded-circle"
                                            :class="participant.is_active ? 'bg-warning-focus text-warning-600' : 'bg-success-focus text-success-600'"
                                            :aria-label="participant.is_active ? 'Nonaktifkan' : 'Aktifkan'"
                                        >
                                            <i :class="participant.is_active ? 'ri-forbid-line' : 'ri-checkbox-circle-line'"></i>
                                        </button>
                                        <button type="button" @click="deleteParticipant(participant)" class="bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-32-px h-32-px d-flex justify-content-center align-items-center rounded-circle" aria-label="Hapus peserta">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredParticipants.length === 0" x-cloak>
                            <td colspan="7" class="text-center text-secondary-light py-40">
                                Tidak ada peserta yang cocok dengan pencarian atau filter kamu.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal tambah peserta --}}
    <div x-show="showModal" x-cloak class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center p-16" style="display:none;z-index:1050;">
        <div @click="showModal = false" class="position-fixed top-0 start-0 w-100 h-100 bg-dark" style="opacity:.5;"></div>

        <div class="position-relative bg-white radius-8 shadow w-100 p-24" style="max-width:480px;">
            <h6 class="fw-bold mb-16">Tambah Peserta</h6>

            <form method="POST" action="{{ route('admin.participants.store') }}" class="d-flex flex-column gap-16">
                @csrf

                <div>
                    <label for="participant_name" class="form-label fw-medium">Nama</label>
                    <input type="text" id="participant_name" name="name" value="{{ old('name') }}" required class="form-control radius-8">
                    @error('name')
                        <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="participant_email" class="form-label fw-medium">Email</label>
                    <input type="email" id="participant_email" name="email" value="{{ old('email') }}" required class="form-control radius-8">
                    @error('email')
                        <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="participant_password" class="form-label fw-medium">Kata Sandi</label>
                    <input type="password" id="participant_password" name="password" required class="form-control radius-8">
                    @error('password')
                        <p class="text-danger-600 text-sm mt-4 mb-0">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="participant_password_confirmation" class="form-label fw-medium">Konfirmasi Kata Sandi</label>
                    <input type="password" id="participant_password_confirmation" name="password_confirmation" required class="form-control radius-8">
                </div>

                <div class="d-flex justify-content-end gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="btn btn-outline-secondary radius-8 px-16 py-8">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-8">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
