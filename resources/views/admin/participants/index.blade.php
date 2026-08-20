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

    @php
        $totalParticipants = $participants->count();
        $activeParticipantCount = $participants->where('is_active', true)->count();
        $inactiveParticipantCount = $participants->where('is_active', false)->count();
    @endphp
    <div class="row gy-3 mb-24">
        <div class="col-6 col-md-4">
            <div class="card radius-8 border h-100 p-20">
                <div class="d-flex align-items-center gap-3">
                    <span class="w-40-px h-40-px flex-shrink-0 d-flex justify-content-center align-items-center radius-8 bg-primary-50 text-primary-600">
                        <i class="ri-team-line"></i>
                    </span>
                    <div>
                        <p class="text-secondary-light text-sm mb-0">Total Peserta</p>
                        <h6 class="fw-bold mb-0">{{ $totalParticipants }}</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card radius-8 border h-100 p-20">
                <div class="d-flex align-items-center gap-3">
                    <span class="w-40-px h-40-px flex-shrink-0 d-flex justify-content-center align-items-center radius-8 bg-success-focus text-success-main">
                        <i class="ri-checkbox-circle-line"></i>
                    </span>
                    <div>
                        <p class="text-secondary-light text-sm mb-0">Aktif</p>
                        <h6 class="fw-bold mb-0">{{ $activeParticipantCount }}</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card radius-8 border h-100 p-20">
                <div class="d-flex align-items-center gap-3">
                    <span class="w-40-px h-40-px flex-shrink-0 d-flex justify-content-center align-items-center radius-8 bg-neutral-200 text-neutral-600">
                        <i class="ri-forbid-line"></i>
                    </span>
                    <div>
                        <p class="text-secondary-light text-sm mb-0">Nonaktif</p>
                        <h6 class="fw-bold mb-0">{{ $inactiveParticipantCount }}</h6>
                    </div>
                </div>
            </div>
        </div>
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
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="w-36-px h-36-px flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle bg-primary-600 text-white fw-semibold text-sm" x-text="participant.name.charAt(0).toUpperCase()"></span>
                                        <span class="fw-semibold text-primary-light" x-text="participant.name"></span>
                                    </div>
                                </td>
                                <td class="text-secondary-light" x-text="participant.email"></td>
                                <td class="text-secondary-light" x-text="participant.joined_at"></td>
                                <td class="text-secondary-light">
                                    <span class="bg-neutral-100 px-12 py-2 radius-4 text-sm" x-text="participant.courses_enrolled"></span>
                                </td>
                                <td class="text-secondary-light">
                                    <span class="bg-success-focus text-success-main px-12 py-2 radius-4 text-sm" x-text="participant.courses_completed"></span>
                                </td>
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
                            <td colspan="7" class="text-center py-60">
                                <i class="ri-user-search-line text-2xl text-neutral-300 d-block mb-8"></i>
                                <p class="text-secondary-light mb-0">Tidak ada peserta yang cocok dengan pencarian atau filter kamu.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{--
        Modal tambah peserta. Div x-show/x-cloak SENGAJA tidak diberi
        class d-flex (lihat catatan panjang di admin/courses/index.blade.php)
        — Bootstrap men-set display:...!important pada class display-*,
        yang mengalahkan x-show Alpine maupun style="display:none" statis,
        jadi modal tidak akan pernah bisa disembunyikan lagi setelah
        Alpine melepas x-cloak. Layout flex-center dipindah ke div
        pembungkus terpisah yang tidak dikontrol x-show.
    --}}
    <div x-show="showModal" x-cloak class="position-fixed top-0 start-0 w-100 h-100" style="display:none;z-index:1050;">
        <div @click="showModal = false" class="position-fixed top-0 start-0 w-100 h-100 bg-dark" style="opacity:.5;"></div>

        <div class="d-flex align-items-center justify-content-center w-100 h-100 p-16">
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
</div>
@endsection
