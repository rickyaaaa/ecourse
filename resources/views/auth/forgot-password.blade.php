@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi — Platform Kursus Online')

@section('content')
<h1 class="font-display text-2xl font-bold tracking-[-0.01em] text-ink">Lupa kata sandi?</h1>
<p class="mt-1.5 text-sm leading-relaxed text-ink-soft">
    Masukkan email akunmu. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
</p>

<form
    method="POST"
    action="{{ route('password.email') }}"
    class="mt-8 space-y-4"
    novalidate
    x-data="{
        email: {{ Js::from(old('email', '')) }},
        submitted: false,
        get emailError() {
            if (! this.submitted) return null;
            if (this.email.trim() === '') return 'Email wajib diisi.';
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email) ? null : 'Format email tidak valid.';
        },
        get isValid() {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email);
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

    <button
        type="submit"
        class="inline-flex w-full items-center justify-center rounded-full bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-500"
    >
        Kirim Tautan Reset
    </button>
</form>

<p class="mt-8 text-center text-sm text-ink-soft">
    Sudah ingat kata sandimu?
    <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">Kembali ke halaman masuk</a>
</p>
@endsection
