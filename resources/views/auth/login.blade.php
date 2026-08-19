@extends('layouts.auth')

@section('title', 'Masuk — Platform Kursus Online')

@section('content')
<h1 class="font-display text-2xl font-bold tracking-[-0.01em] text-ink">Selamat datang kembali</h1>
<p class="mt-1.5 text-sm text-ink-soft">Masuk ke akunmu untuk melanjutkan belajar.</p>

<form
    method="POST"
    action="{{ route('login.store') }}"
    class="mt-8 space-y-4"
    novalidate
    x-data="{
        email: {{ Js::from(old('email', '')) }},
        password: '',
        submitted: false,
        get emailError() {
            if (! this.submitted) return null;
            if (this.email.trim() === '') return 'Email wajib diisi.';
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email) ? null : 'Format email tidak valid.';
        },
        get passwordError() {
            if (! this.submitted) return null;
            return this.password.length === 0 ? 'Kata sandi wajib diisi.' : null;
        },
        get isValid() {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email) && this.password.length > 0;
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
        <label for="email" class="block text-sm font-medium text-ink">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            autofocus
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
        <div class="flex items-center justify-between">
            <label for="password" class="block text-sm font-medium text-ink">Kata Sandi</label>
            <a href="{{ route('password.request') }}" class="text-xs font-medium text-brand-600 hover:underline">Lupa kata sandi?</a>
        </div>
        <input
            type="password"
            id="password"
            name="password"
            autocomplete="current-password"
            placeholder="Kata sandi kamu"
            x-model="password"
            class="mt-1.5 block w-full rounded-xl border px-3.5 py-2.5 text-sm placeholder:text-ink-soft focus:outline-none focus:ring-2"
            :class="passwordError ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-black/10 focus:border-brand-400 focus:ring-brand-100'"
        >
        <p x-show="passwordError" x-cloak class="mt-1 text-xs text-red-600" x-text="passwordError"></p>
    </div>

    <label class="flex items-center gap-2 text-sm text-ink-soft">
        <input type="checkbox" name="remember" class="rounded border-black/20 text-brand-600 focus:ring-brand-400">
        Ingat saya
    </label>

    <button
        type="submit"
        class="inline-flex w-full items-center justify-center rounded-full bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-500"
    >
        Masuk
    </button>
</form>

<p class="mt-8 text-center text-sm text-ink-soft">
    Belum punya akun?
    <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:underline">Daftar gratis</a>
</p>
@endsection
