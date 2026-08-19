@extends('layouts.app')

@section('title', 'Profil Saya — Platform Kursus Online')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Profil Saya</h1>
    <p class="mt-2 text-gray-600">Kelola data diri dan kata sandi akunmu di sini.</p>

    <div class="mt-8 rounded-xl border border-gray-200 bg-white p-6 sm:p-8">
        <h2 class="text-lg font-semibold text-gray-900">Data Diri</h2>
        <p class="mt-1 text-sm text-gray-500">Perbarui nama dan alamat email akunmu.</p>

        <form
            method="POST"
            action="{{ route('profile.update') }}"
            class="mt-6 space-y-4"
            novalidate
            x-data="{
                name: {{ Js::from(old('name', $user->name)) }},
                email: {{ Js::from(old('email', $user->email)) }},
                submitted: false,
                get nameError() {
                    if (! this.submitted) return null;
                    return this.name.trim() === '' ? 'Nama wajib diisi.' : null;
                },
                get emailError() {
                    if (! this.submitted) return null;
                    if (this.email.trim() === '') return 'Email wajib diisi.';
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email) ? null : 'Format email tidak valid.';
                },
                get isValid() {
                    return this.name.trim() !== '' && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email);
                },
                handleSubmit() {
                    this.submitted = true;

                    if (this.isValid) {
                        this.$el.submit();
                    }
                },
            }"
            @submit.prevent="handleSubmit()"
        >
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    x-model="name"
                    class="mt-1 block w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-1"
                    :class="nameError ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'"
                >
                <p x-show="nameError" x-cloak class="mt-1 text-xs text-red-600" x-text="nameError"></p>
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    x-model="email"
                    class="mt-1 block w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-1"
                    :class="emailError ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'"
                >
                <p x-show="emailError" x-cloak class="mt-1 text-xs text-red-600" x-text="emailError"></p>
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500"
            >
                Simpan Perubahan
            </button>
        </form>
    </div>

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 sm:p-8">
        <h2 class="text-lg font-semibold text-gray-900">Ubah Kata Sandi</h2>
        <p class="mt-1 text-sm text-gray-500">Pastikan kata sandi baru minimal 8 karakter.</p>

        <form
            method="POST"
            action="{{ route('profile.password.update') }}"
            class="mt-6 space-y-4"
            novalidate
            x-data="{
                currentPassword: '',
                newPassword: '',
                newPasswordConfirmation: '',
                submitted: false,
                get currentPasswordError() {
                    if (! this.submitted) return null;
                    return this.currentPassword.length === 0 ? 'Kata sandi saat ini wajib diisi.' : null;
                },
                get newPasswordError() {
                    if (! this.submitted) return null;
                    if (this.newPassword.length === 0) return 'Kata sandi baru wajib diisi.';
                    return this.newPassword.length < 8 ? 'Kata sandi baru minimal 8 karakter.' : null;
                },
                get confirmationError() {
                    if (! this.submitted) return null;
                    return this.newPassword !== this.newPasswordConfirmation ? 'Konfirmasi kata sandi baru tidak cocok.' : null;
                },
                get isValid() {
                    return this.currentPassword.length > 0
                        && this.newPassword.length >= 8
                        && this.newPassword === this.newPasswordConfirmation;
                },
                handleSubmit() {
                    this.submitted = true;

                    if (this.isValid) {
                        this.$el.submit();
                    }
                },
            }"
            @submit.prevent="handleSubmit()"
        >
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Kata Sandi Saat Ini</label>
                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    autocomplete="current-password"
                    x-model="currentPassword"
                    class="mt-1 block w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-1"
                    :class="currentPasswordError ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'"
                >
                <p x-show="currentPasswordError" x-cloak class="mt-1 text-xs text-red-600" x-text="currentPasswordError"></p>
                @error('current_password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi Baru</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    x-model="newPassword"
                    class="mt-1 block w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-1"
                    :class="newPasswordError ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'"
                >
                <p x-show="newPasswordError" x-cloak class="mt-1 text-xs text-red-600" x-text="newPasswordError"></p>
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                    Konfirmasi Kata Sandi Baru
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                    x-model="newPasswordConfirmation"
                    class="mt-1 block w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-1"
                    :class="confirmationError ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'"
                >
                <p x-show="confirmationError" x-cloak class="mt-1 text-xs text-red-600" x-text="confirmationError"></p>
            </div>

            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500"
            >
                Ubah Kata Sandi
            </button>
        </form>
    </div>
</div>
@endsection
