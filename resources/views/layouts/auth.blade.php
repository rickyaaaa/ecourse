<!doctype html>
<html class="no-js" lang="id" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Platform Kursus Online')</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('escul/assets/img/logo-icon.svg') }}">

    <link rel="stylesheet" href="{{ asset('escul/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('escul/assets/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('escul/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('escul/assets/css/app-overrides.css') }}">
</head>

<body class="theme-style4">
    <div class="row g-0 min-vh-100">
        {{-- Panel visual — kiri, disembunyikan di layar kecil supaya form jadi fokus utama --}}
        <div class="col-lg-5 d-none d-lg-block position-relative overflow-hidden bg-theme">
            <div class="h-100 d-flex flex-column justify-content-between p-5" style="min-height:100vh;">
                <a href="{{ route('courses.index') }}" class="d-inline-flex align-items-center gap-2 text-white text-decoration-none fw-bold fs-5">
                    <img src="{{ asset('escul/assets/img/logo-icon.svg') }}" alt="Platform Kursus" height="36">
                    Platform Kursus
                </a>

                <div>
                    <img src="{{ asset('escul/assets/img/normal/about_5_3.jpg') }}" alt="Suasana belajar online" class="rounded-4 w-100 mb-4" style="max-height:280px;object-fit:cover;">

                    <h2 class="text-white h3">Belajar online lebih terarah &amp; mudah dipantau</h2>
                    <div class="checklist style3">
                        <ul>
                            <li class="text-white">Materi video, PDF, dan kuis dalam satu tempat</li>
                            <li class="text-white">Progres belajar tersimpan otomatis</li>
                            <li class="text-white">Belajar kapan saja, dengan ritmemu sendiri</li>
                        </ul>
                    </div>
                </div>

                <p class="text-white-50 small mb-0">&copy; {{ date('Y') }} Platform Kursus Online</p>
            </div>
        </div>

        {{-- Panel form — kanan --}}
        <div class="col-lg-7">
            <div class="d-flex flex-column min-vh-100 px-4 px-lg-5 py-5">
                <a href="{{ route('courses.index') }}" class="d-inline-flex d-lg-none align-items-center gap-2 text-decoration-none fw-bold fs-5 text-title mb-4">
                    <img src="{{ asset('escul/assets/img/logo-icon.svg') }}" alt="Platform Kursus" height="36">
                    Platform Kursus
                </a>

                @if (session('notice'))
                    <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
                        <i class="fal fa-circle-check"></i>{{ session('notice') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
                        <i class="fal fa-circle-exclamation"></i>{{ session('error') }}
                    </div>
                @endif

                <div class="flex-grow-1 d-flex align-items-center">
                    <div class="w-100 mx-auto" style="max-width:420px;">
                        @yield('content')
                    </div>
                </div>

                <p class="text-body small mb-0">
                    <a href="{{ route('courses.index') }}" class="text-inherit"><i class="fal fa-arrow-left me-1"></i>Kembali ke Katalog Kursus</a>
                </p>
            </div>
        </div>
    </div>

    <script src="{{ asset('escul/assets/js/vendor/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('escul/assets/js/bootstrap.min.js') }}"></script>
    @vite(['resources/js/app.js'])
</body>

</html>
