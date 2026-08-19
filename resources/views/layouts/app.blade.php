<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Platform Kursus Online')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('courses.index') }}" class="text-lg font-bold text-indigo-600">
                Platform Kursus
            </a>

            <nav class="flex items-center gap-4 text-sm font-medium text-gray-600">
                <a href="{{ route('courses.index') }}" class="hover:text-indigo-600">Katalog Kursus</a>

                @auth
                    <a href="{{ route('dashboard.index') }}" class="hover:text-indigo-600">Dasbor Saya</a>
                    <a href="{{ route('profile.edit') }}" class="hover:text-indigo-600">Profil Saya</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600">Panel Admin</a>
                    @endif
                    <span class="hidden text-gray-400 sm:inline">|</span>
                    <span class="hidden text-gray-500 sm:inline">Hai, {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('register') }}" class="hover:text-indigo-600">Daftar</a>
                    <a href="{{ route('login') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-500">Masuk</a>
                @endauth
            </nav>
        </div>
    </header>

    @if (session('notice'))
        <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-md border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
                {{ session('notice') }}
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="mt-16 border-t border-gray-200 bg-white py-8">
        <div class="mx-auto max-w-7xl px-4 text-center text-sm text-gray-500 sm:px-6 lg:px-8">
            &copy; {{ date('Y') }} Platform Kursus Online. Semua hak dilindungi.
        </div>
    </footer>
</body>
</html>
