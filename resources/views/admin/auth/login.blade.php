@extends('layouts.auth')

@section('title', 'Masuk Admin — Platform Kursus Online')

@section('content')
<h1 class="h2 mb-1">Masuk sebagai Admin</h1>
<p class="text-body mb-4">Halaman ini khusus untuk pengelola platform, terpisah dari login peserta.</p>

<form
    method="POST"
    action="{{ route('admin.login.store') }}"
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

    <div class="form-group style-border3 mb-3">
        <label for="email" class="form-label">Email</label>
        <div class="position-relative">
            <input
                type="email"
                id="email"
                name="email"
                autofocus
                autocomplete="email"
                autocapitalize="off"
                autocorrect="off"
                spellcheck="false"
                placeholder="admin@email.com"
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

    <div class="form-group style-border3 mb-2">
        <label for="password" class="form-label">Kata Sandi</label>
        <div class="position-relative">
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                placeholder="Kata sandi kamu"
                x-model="password"
                class="form-control"
                :class="passwordError && 'is-invalid'"
            >
            <i class="fal fa-lock field-icon"></i>
        </div>
        <p x-show="passwordError" x-cloak class="text-danger small mt-1 mb-0" x-text="passwordError"></p>
    </div>

    <div class="form-check mb-4">
        <input type="checkbox" name="remember" class="form-check-input" id="remember">
        <label class="form-check-label small text-body" for="remember">Ingat saya</label>
    </div>

    <button type="submit" class="th-btn w-100 justify-content-center">
        MASUK
    </button>
</form>
@endsection
