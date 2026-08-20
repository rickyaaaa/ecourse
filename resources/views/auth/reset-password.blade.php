@extends('layouts.auth')

@section('title', 'Atur Kata Sandi Baru — Platform Kursus Online')

@section('content')
<h1 class="h2 mb-1">Atur Kata Sandi Baru</h1>
<p class="text-body mb-4">Buat kata sandi baru untuk akunmu.</p>

<form
    method="POST"
    action="{{ route('password.update') }}"
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

    <div class="form-group style-border3 mb-3">
        <label for="email" class="form-label">Email</label>
        <div class="position-relative">
            <input
                type="email"
                id="email"
                name="email"
                required
                readonly
                value="{{ $email }}"
                class="form-control bg-light"
            >
            <i class="fal fa-envelope field-icon"></i>
        </div>
        @error('email')
            <p class="text-danger small mt-1 mb-0">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group style-border3 mb-3">
        <label for="password" class="form-label">Kata Sandi Baru</label>
        <div class="position-relative">
            <input
                type="password"
                id="password"
                name="password"
                autofocus
                autocomplete="new-password"
                placeholder="Minimal 8 karakter"
                x-model="password"
                class="form-control"
                :class="passwordError && 'is-invalid'"
            >
            <i class="fal fa-lock field-icon"></i>
        </div>
        <p x-show="passwordError" x-cloak class="text-danger small mt-1 mb-0" x-text="passwordError"></p>
        @error('password')
            <p class="text-danger small mt-1 mb-0">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group style-border3 mb-2">
        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
        <div class="position-relative">
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                autocomplete="new-password"
                placeholder="Ulangi kata sandi baru"
                x-model="passwordConfirmation"
                class="form-control"
                :class="confirmationError && 'is-invalid'"
            >
            <i class="fal fa-lock field-icon"></i>
        </div>
        <p x-show="confirmationError" x-cloak class="text-danger small mt-1 mb-0" x-text="confirmationError"></p>
    </div>

    <button type="submit" class="th-btn w-100 justify-content-center mt-3">
        SIMPAN KATA SANDI BARU
    </button>
</form>
@endsection
