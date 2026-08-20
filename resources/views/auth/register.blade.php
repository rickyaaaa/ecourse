@extends('layouts.auth')

@section('title', 'Daftar Akun — Platform Kursus Online')

@section('content')
<h1 class="h2 mb-1">Buat Akun Barumu</h1>
<p class="text-body mb-4">Daftar gratis dan mulai belajar hari ini juga.</p>

<form
    method="POST"
    action="{{ route('register.store') }}"
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

    <div class="form-group style-border3 mb-3">
        <label for="name" class="form-label">Nama Lengkap</label>
        <div class="position-relative">
            <input
                type="text"
                id="name"
                name="name"
                autofocus
                autocomplete="name"
                placeholder="Nama kamu"
                x-model="name"
                class="form-control"
                :class="nameError && 'is-invalid'"
            >
            <i class="fal fa-user field-icon"></i>
        </div>
        <p x-show="nameError" x-cloak class="text-danger small mt-1 mb-0" x-text="nameError"></p>
        @error('name')
            <p class="text-danger small mt-1 mb-0">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group style-border3 mb-3">
        <label for="email" class="form-label">Email</label>
        <div class="position-relative">
            <input
                type="email"
                id="email"
                name="email"
                autocomplete="email"
                autocapitalize="off"
                autocorrect="off"
                spellcheck="false"
                placeholder="nama@email.com"
                x-model="email"
                class="form-control"
                :class="emailError && 'is-invalid'"
            >
            <i class="fal fa-envelope field-icon"></i>
        </div>
        <p x-show="emailError" x-cloak class="text-danger small mt-1 mb-0" x-text="emailError"></p>
        @error('email')
            <p class="text-danger small mt-1 mb-0">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group style-border3 mb-3">
        <label for="password" class="form-label">Kata Sandi</label>
        <div class="position-relative">
            <input
                type="password"
                id="password"
                name="password"
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
        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
        <div class="position-relative">
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                autocomplete="new-password"
                placeholder="Ulangi kata sandi"
                x-model="passwordConfirmation"
                class="form-control"
                :class="passwordConfirmationError && 'is-invalid'"
            >
            <i class="fal fa-lock field-icon"></i>
        </div>
        <p x-show="passwordConfirmationError" x-cloak class="text-danger small mt-1 mb-0" x-text="passwordConfirmationError"></p>
    </div>

    <button type="submit" class="th-btn w-100 justify-content-center mt-3">
        DAFTAR
    </button>
</form>

<p class="text-body small text-center mt-4 mb-0">
    Sudah punya akun?
    <a href="{{ route('login') }}" class="fw-semibold">Masuk</a>
</p>
@endsection
