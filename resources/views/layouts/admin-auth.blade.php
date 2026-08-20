<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin — Platform Kursus Online')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col items-center justify-center bg-gray-900 px-4 py-12 text-gray-900 antialiased">
    <div class="w-full max-w-sm">
        <div class="mb-6 text-center">
            <span class="inline-flex items-center gap-2 text-lg font-bold text-white">
                🛡️ Panel Admin
            </span>
            <p class="mt-1 text-sm text-gray-400">Platform Kursus Online</p>
        </div>

        @if (session('notice'))
            <div class="mb-4 rounded-md border border-indigo-800 bg-indigo-950 px-4 py-3 text-sm text-indigo-200">
                {{ session('notice') }}
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-xl sm:p-8">
            @yield('content')
        </div>

        <p class="mt-6 text-center text-sm text-gray-400">
            <a href="{{ route('courses.index') }}" class="hover:text-gray-200">&larr; Kembali ke situs</a>
        </p>
    </div>
</body>
</html>
