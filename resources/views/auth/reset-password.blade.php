@extends('layouts.auth')

@section('title', 'Atur Kata Sandi Baru — Platform Kursus Online')

@section('content')
<h1 class="font-display text-2xl font-bold tracking-[-0.01em] text-ink">Atur kata sandi baru</h1>
<p class="mt-1.5 text-sm text-ink-soft">Buat kata sandi baru untuk akunmu.</p>

<form
    method="POST"
    action="{{ route('password.update') }}"
    class="mt-8 space-y-4"
    novalidate
    x-data="{
        password: '',
        passwordConfirmation: '',
        submitted: false,
        get passwordError() {
            if (! this.submitted) return null;
            if (this.password.length === 0) return 'Kata sandi baru wajib diisi.';
            return this.password.length < 8 ? 'Kata sandi minimal 8 karakter.' : null;
        },
        get confirmationError() {
            if (! this.submitted) return null;
            if (this.passwordConfirmation.length === 0) return 'Konfirmasi kata sandi wajib diisi.';
            return this.password !== this.passwordConfirmation ? 'Konfirmasi kata sandi tidak cocok.' : null;
        },
        get isValid() {
            return this.password.length >= 8 && this.password === this.passwordConfirmation;
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
    <input type="hidden" name="token" value="{{ $token }}">

    <div>
        <label for="email" class="block text-sm font-medium text-ink">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            required
            readonly
            value="{{ $email }}"
            class="mt-1.5 block w-full rounded-xl border border-black/10 bg-black/[0.03] px-3.5 py-2.5 text-sm text-ink-soft focus:outline-none"
        >
        @error('email')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-ink">Kata Sandi Baru</label>
        <input
            type="password"
            id="password"
            name="password"
            autofocus
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
            Konfirmasi Kata Sandi Baru
        </label>
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            autocomplete="new-password"
            placeholder="Ulangi kata sandi baru"
            x-model="passwordConfirmation"
            class="mt-1.5 block w-full rounded-xl border px-3.5 py-2.5 text-sm placeholder:text-ink-soft focus:outline-none focus:ring-2"
            :class="confirmationError ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-black/10 focus:border-brand-400 focus:ring-brand-100'"
        >
        <p x-show="confirmationError" x-cloak class="mt-1 text-xs text-red-600" x-text="confirmationError"></p>
    </div>

    <button
        type="submit"
        class="inline-flex w-full items-center justify-center rounded-full bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-500"
    >
        Simpan Kata Sandi Baru
    </button>
</form>
@endsection
