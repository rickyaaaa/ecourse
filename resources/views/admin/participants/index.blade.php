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
            form.innerHTML = `
                <input type=\"hidden\" name=\"_token\" value=\"${document.querySelector('meta[name=csrf-token]').content}\">
                <input type=\"hidden\" name=\"_method\" value=\"${method}\">
            `;
            document.body.appendChild(form);
            form.submit();
        },
        toggleStatus(participant) {
            this.submitAction(`/admin/peserta/${participant.id}/status`, 'PUT');
        },
        deleteParticipant(participant) {
            if (! confirm(`Hapus peserta \"${participant.name}\"? Tindakan ini tidak bisa dibatalkan.`)) return;
            this.submitAction(`/admin/peserta/${participant.id}`, 'DELETE');
        },
    }"
    x-init="@if ($errors->any()) showModal = true; @endif"
>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Daftar Peserta</h2>
            <p class="mt-1 text-sm text-gray-500">Kelola akun pelajar yang terdaftar di platform.</p>
        </div>
        <button
            @click="openCreateModal()"
            class="inline-flex items-center justify-center gap-1 rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500"
        >
            + Tambah Peserta
        </button>
    </div>

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <label class="relative flex-1">
            <span class="sr-only">Cari peserta</span>
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">🔎</span>
            <input
                type="search"
                x-model="search"
                placeholder="Cari nama atau email peserta..."
                class="w-full rounded-md border border-gray-300 py-2 pl-9 pr-4 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
        </label>

        <select
            x-model="statusFilter"
            class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:w-44"
        >
            <option value="">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
        </select>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Bergabung</th>
                    <th class="px-4 py-3">Kursus Diikuti</th>
                    <th class="px-4 py-3">Kursus Selesai</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-for="participant in filteredParticipants" :key="participant.id">
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900" x-text="participant.name"></td>
                        <td class="px-4 py-3 text-gray-600" x-text="participant.email"></td>
                        <td class="px-4 py-3 text-gray-600" x-text="participant.joined_at"></td>
                        <td class="px-4 py-3 text-gray-600" x-text="participant.courses_enrolled"></td>
                        <td class="px-4 py-3 text-gray-600" x-text="participant.courses_completed"></td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="participant.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                x-text="participant.is_active ? 'Aktif' : 'Nonaktif'"
                            ></span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button @click="toggleStatus(participant)" class="font-medium text-indigo-600 hover:underline">
                                <span x-text="participant.is_active ? 'Nonaktifkan' : 'Aktifkan'"></span>
                            </button>
                            <span class="mx-1 text-gray-300">|</span>
                            <button @click="deleteParticipant(participant)" class="font-medium text-red-600 hover:underline">Hapus</button>
                        </td>
                    </tr>
                </template>

                <tr x-show="filteredParticipants.length === 0" x-cloak>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                        Tidak ada peserta yang cocok dengan pencarian atau filter kamu.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Modal tambah peserta --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div @click="showModal = false" class="fixed inset-0 bg-gray-900/50"></div>

        <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-lg">
            <h3 class="text-lg font-semibold text-gray-900">Tambah Peserta</h3>

            <form method="POST" action="{{ route('admin.participants.store') }}" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label for="participant_name" class="block text-sm font-medium text-gray-700">Nama</label>
                    <input
                        type="text"
                        id="participant_name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="participant_email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input
                        type="email"
                        id="participant_email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="participant_password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                    <input
                        type="password"
                        id="participant_password"
                        name="password"
                        required
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    >
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="participant_password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Kata Sandi</label>
                    <input
                        type="password"
                        id="participant_password_confirmation"
                        name="password_confirmation"
                        required
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    >
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
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
@endsection
