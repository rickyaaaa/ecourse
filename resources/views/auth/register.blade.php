@extends('layouts.auth')

@section('title', 'Daftar Akun — Platform Kursus Online')

@section('content')
<h1 class="font-display text-2xl font-bold tracking-[-0.01em] text-ink">Buat akun barumu</h1>
<p class="mt-1.5 text-sm text-ink-soft">Daftar gratis dan mulai belajar hari ini juga.</p>

<form
    method="POST"
    action="{{ route('register.store') }}"
    class="mt-8 space-y-4"
    novalidate
    x-data="{
        name: {{ Js::from(old('name', '')) }},
        email: {{ Js::from(old('email', '')) }},
        password: '',
        passwordConfirmation: '',
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
        get passwordError() {
            if (! this.submitted) return null;
            if (this.password.length === 0) return 'Kata sandi wajib diisi.';
            return this.password.length < 8 ? 'Kata sandi minimal 8 karakter.' : null;
        },
        get passwordConfirmationError() {
            if (! this.submitted) return null;
            if (this.passwordConfirmation.length === 0) return 'Konfirmasi kata sandi wajib diisi.';
            return this.password !== this.passwordConfirmation ? 'Konfirmasi kata sandi tidak cocok.' : null;
        },
        get isValid() {
            return this.name.trim() !== ''
                && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)
                && this.password.length >= 8
                && this.password === this.passwordConfirmation;
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

    <div>
        <label for="name" class="block text-sm font-medium text-ink">Nama Lengkap</label>
        <input
            type="text"
            id="name"
            name="name"
            autofocus
            autocomplete="name"
            placeholder="Nama kamu"
            x-model="name"
            class="mt-1.5 block w-full rounded-xl border px-3.5 py-2.5 text-sm placeholder:text-ink-soft focus:outline-none focus:ring-2"
            :class="nameError ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-black/10 focus:border-brand-400 focus:ring-brand-100'"
        >
        <p x-show="nameError" x-cloak class="mt-1 text-xs text-red-600" x-text="nameError"></p>
        @error('name')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-ink">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            autocomplete="email"
            placeholder="nama@email.com"
            x-model="email"
            class="mt-1.5 block w-full rounded-xl border px-3.5 py-2.5 text-sm placeholder:text-ink-soft focus:outline-none focus:ring-2"
            :class="emailError ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-black/10 focus:border-brand-400 focus:ring-brand-100'"
        >
        <p x-show="emailError" x-cloak class="mt-1 text-xs text-red-600" x-text="emailError"></p>
        @error('email')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-ink">Kata Sandi</label>
        <input
            type="password"
            id="password"
            name="password"
            autocomplete="new-password"
            placeholder="Minimal 8 karakter"
            x-model="password"
            class="mt-1.5 block w-full rounded-xl border px-3.5 py-2.5 text-sm placeholder:text-ink-soft focus:outline-none focus:ring-2"
            :class="passwordError ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-black/10 focus:border-brand-400 focus:ring-brand-100'"
        >
        <p x-show="passwordError" x-cloak class="mt-1 text-xs text-red-600" x-text="passwordError"></p>
        @error('password')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-ink">
            Konfirmasi Kata Sandi
        </label>
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            autocomplete="new-password"
            placeholder="Ulangi kata sandi"
            x-model="passwordConfirmation"
            class="mt-1.5 block w-full rounded-xl border px-3.5 py-2.5 text-sm placeholder:text-ink-soft focus:outline-none focus:ring-2"
            :class="passwordConfirmationError ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-black/10 focus:border-brand-400 focus:ring-brand-100'"
        >
        <p x-show="passwordConfirmationError" x-cloak class="mt-1 text-xs text-red-600" x-text="passwordConfirmationError"></p>
    </div>

    <button
        type="submit"
        class="inline-flex w-full items-center justify-center rounded-full bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-500"
    >
        Daftar
    </button>
</form>

<p class="mt-8 text-center text-sm text-ink-soft">
    Sudah punya akun?
    <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">Masuk</a>
</p>
@endsection
