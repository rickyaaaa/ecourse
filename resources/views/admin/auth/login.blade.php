@extends('layouts.admin-auth')

@section('title', 'Masuk Admin — Platform Kursus Online')

@section('content')
<h1 class="mb-1 text-lg font-semibold text-gray-900">Masuk sebagai Admin</h1>
<p class="mb-6 text-sm text-gray-500">Halaman ini khusus untuk pengelola platform, terpisah dari login peserta.</p>

<form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
    @csrf

    <div>
        <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            autofocus
            autocomplete="email"
            autocapitalize="off"
            autocorrect="off"
            spellcheck="false"
            placeholder="admin@email.com"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('email') border-red-400 @enderror"
        >
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="mb-1 block text-sm font-medium text-gray-700">Kata Sandi</label>
        <input
            type="password"
            id="password"
            name="password"
            autocomplete="current-password"
            placeholder="Kata sandi kamu"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
        >
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <label for="remember" class="text-sm text-gray-600">Ingat saya</label>
    </div>

    <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
        MASUK
    </button>
</form>
@endsection
