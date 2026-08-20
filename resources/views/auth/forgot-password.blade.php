@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi — Platform Kursus Online')

@section('content')
<h1 class="h2 mb-1">Lupa Kata Sandi?</h1>
<p class="text-body mb-4">Masukkan email akunmu. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.</p>

<form
    method="POST"
    action="{{ route('password.email') }}"
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

    <div class="form-group style-border3 mb-2">
        <label for="email" class="form-label">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            autofocus
            autocomplete="email"
            autocapitalize="off"
            autocorrect="off"
            spellcheck="false"
            placeholder="nama@email.com"
            x-model="email"
            class="form-control"
            :class="emailError && 'is-invalid'"
        >
        <i class="fal fa-envelope"></i>
        <p x-show="emailError" x-cloak class="text-danger small mt-1 mb-0" x-text="emailError"></p>
        @error('email')
            <p class="text-danger small mt-1 mb-0">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="th-btn w-100 justify-content-center mt-3">
        KIRIM TAUTAN RESET
    </button>
</form>

<p class="text-body small text-center mt-4 mb-0">
    Sudah ingat kata sandimu?
    <a href="{{ route('login') }}" class="fw-semibold">Kembali ke halaman masuk</a>
</p>
@endsection
