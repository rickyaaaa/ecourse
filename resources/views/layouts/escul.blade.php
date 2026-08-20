<!doctype html>
<html class="no-js" lang="id" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Platform Kursus Online')</title>
    <meta name="description" content="@yield('meta_description', 'Belajar online lebih terarah, fleksibel, dan mudah dipantau — materi video, PDF, kuis, dan progres belajar dalam satu platform.')">

    <link rel="icon" type="image/svg+xml" href="{{ asset('escul/assets/img/logo-icon.svg') }}">

    {{-- CSS template Escul — dimuat lokal dari public/escul/assets, bukan CDN --}}
    <link rel="stylesheet" href="{{ asset('escul/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('escul/assets/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('escul/assets/css/magnific-popup.min.css') }}">
    <link rel="stylesheet" href="{{ asset('escul/assets/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('escul/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('escul/assets/css/app-overrides.css') }}">

    @stack('styles')
</head>

<body class="theme-style4">

    {{-- Preloader --}}
    <div class="preloader">
        <button class="th-btn preloaderCls">Lewati</button>
        <div class="preloader-inner">
            <div class="bounce mb-4">
                <img src="{{ asset('escul/assets/img/logo-icon.svg') }}" alt="Platform Kursus" width="60">
            </div>
            <span class="loader">
                Platform Kursus
                <span class="loading-text">Platform Kursus</span>
            </span>
        </div>
    </div>

    {{-- Menu mobile (off-canvas) --}}
    <div class="th-menu-wrapper">
        <div class="th-menu-area text-center">
            <button class="th-menu-toggle"><i class="fal fa-times"></i></button>
            <div class="th-menu-content">
                <div class="mobile-logo">
                    <a href="{{ route('courses.index') }}" class="d-inline-flex align-items-center gap-2">
                        <img src="{{ asset('escul/assets/img/logo-icon.svg') }}" alt="Platform Kursus" height="36">
                        <span class="fw-bold">Platform Kursus</span>
                    </a>
                </div>
                <div class="th-mobile-menu-bottom">
                    <div class="btn-wrap">
                        @auth
                            <a href="{{ route('dashboard.index') }}" class="nav-btn nav-btn-solid w-100 justify-content-center">DASBOR SAYA</a>
                        @else
                            <a href="{{ route('register') }}" class="nav-btn nav-btn-solid w-100 justify-content-center">DAFTAR GRATIS</a>
                        @endauth
                    </div>
                </div>
                <div class="th-mobile-menu">
                    <ul>
                        <li><a href="{{ route('courses.index') }}">Beranda</a></li>
                        <li><a href="{{ $anchor('katalog') }}">Kursus</a></li>
                        <li><a href="{{ $anchor('keunggulan') }}">Keunggulan</a></li>
                        <li><a href="{{ $anchor('cara-belajar') }}">Cara Belajar</a></li>
                        <li><a href="{{ $anchor('kontak') }}">Kontak</a></li>
                        @auth
                            <li><a href="{{ route('dashboard.index') }}">Dasbor Saya</a></li>
                            <li><a href="{{ route('dashboard.enrolledCourses') }}">Kursus Saya</a></li>
                            <li><a href="{{ route('quizzes.history') }}">Riwayat Kuis</a></li>
                            <li><a href="{{ route('profile.edit') }}">Profil Saya</a></li>
                            @if (auth()->user()->isAdmin())
                                <li><a href="{{ route('admin.dashboard') }}">Panel Admin</a></li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="th-mobile-logout-btn">Keluar</button>
                                </form>
                            </li>
                        @else
                            <li><a href="{{ route('login') }}">Masuk</a></li>
                            <li><a href="{{ route('register') }}">Daftar</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <header class="th-header header-default onepage-nav">
        <div class="header-top">
            <div class="header-top-bg" data-mask-src="{{ asset('escul/assets/img/shape/header-top-bg-mask1-1.png') }}"></div>
            <div class="container">
                <div class="row justify-content-center justify-content-lg-between align-items-center gy-2">
                    <div class="col-auto d-none d-lg-block">
                        <div class="header-links">
                            <ul class="header-left-wrap">
                                <li><i class="fa-regular fa-envelope"></i><a href="mailto:halo@platformkursus.id">halo@platformkursus.id</a></li>
                                <li><i class="fa-regular fa-clock"></i>Senin–Sabtu, 08.00–20.00 WIB</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="header-links ps-0">
                            <ul>
                                @auth
                                    <li><i class="fas fa-user"></i>Hai, {{ Str::of(auth()->user()->name)->before(' ') }}</li>
                                @else
                                    <li><i class="fas fa-user"></i><a href="{{ route('login') }}">Masuk</a> / <a href="{{ route('register') }}">Daftar</a></li>
                                @endauth
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sticky-wrapper">
            <div class="container">
                <div class="menu-area">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto">
                            <div class="header-logo">
                                <a href="{{ route('courses.index') }}" class="d-inline-flex align-items-center gap-2">
                                    <img src="{{ asset('escul/assets/img/logo-icon.svg') }}" alt="Platform Kursus" height="40">
                                    <span class="fw-bold fs-5 text-title">Platform Kursus</span>
                                </a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <nav class="main-menu d-none d-lg-inline-block">
                                <ul>
                                    <li><a href="{{ route('courses.index') }}">Beranda</a></li>
                                    <li><a href="{{ $anchor('katalog') }}">Kursus</a></li>
                                    <li><a href="{{ $anchor('keunggulan') }}">Keunggulan</a></li>
                                    <li><a href="{{ $anchor('cara-belajar') }}">Cara Belajar</a></li>
                                    <li><a href="{{ $anchor('kontak') }}">Kontak</a></li>
                                </ul>
                            </nav>
                            <button type="button" class="th-menu-toggle d-block d-lg-none"><i class="fal fa-bars"></i></button>
                        </div>
                        <div class="col-auto d-none d-lg-block">
                            <div class="header-button">
                                @auth
                                    <div class="dropdown-link">
                                        <a class="nav-btn nav-btn-outline" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fal fa-user"></i>{{ Str::of(auth()->user()->name)->before(' ') }}
                                            <i class="fal fa-chevron-down nav-btn-caret"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('dashboard.index') }}"><i class="fal fa-gauge me-2"></i>Dasbor Saya</a></li>
                                            <li><a class="dropdown-item" href="{{ route('dashboard.enrolledCourses') }}"><i class="fal fa-book me-2"></i>Kursus Saya</a></li>
                                            <li><a class="dropdown-item" href="{{ route('quizzes.history') }}"><i class="fal fa-clipboard-check me-2"></i>Riwayat Kuis</a></li>
                                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fal fa-user-gear me-2"></i>Profil Saya</a></li>
                                            @if (auth()->user()->isAdmin())
                                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fal fa-shield-check me-2"></i>Panel Admin</a></li>
                                            @endif
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('logout') }}">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fal fa-arrow-right-from-bracket me-2"></i>Keluar</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                @else
                                    <a href="{{ route('login') }}" class="nav-btn nav-btn-outline">MASUK</a>
                                    <a href="{{ route('register') }}" class="nav-btn nav-btn-solid">DAFTAR GRATIS</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    @if (session('notice'))
        <div class="container mt-4">
            <div class="alert alert-success d-flex align-items-center gap-2 mb-0" role="alert">
                <i class="fal fa-circle-check"></i>{{ session('notice') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="container mt-4">
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-0" role="alert">
                <i class="fal fa-circle-exclamation"></i>{{ session('error') }}
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="footer-wrapper footer-layout4">
        <div class="footer-bottom" data-bg-src="{{ asset('escul/assets/img/bg/footer-bg4-1.jpg') }}">
            <div class="widget-area space-top" id="kontak">
                <div class="container">
                    <div class="row justify-content-between gy-4">
                        <div class="col-md-6 col-xl-4">
                            <div class="widget footer-widget">
                                <div class="th-widget-about style2">
                                    <div class="about-logo">
                                        <a href="{{ route('courses.index') }}" class="d-inline-flex align-items-center gap-2">
                                            <img src="{{ asset('escul/assets/img/logo-icon.svg') }}" alt="Platform Kursus" height="40">
                                            <span class="fw-bold fs-5 text-white">Platform Kursus</span>
                                        </a>
                                    </div>
                                    <p class="about-text">Belajar online lebih terarah, fleksibel, dan mudah dipantau — materi video, PDF, kuis, dan progres belajar dalam satu platform.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-auto">
                            <div class="widget widget_nav_menu footer-widget">
                                <h3 class="widget_title">Jelajah</h3>
                                <div class="menu-all-pages-container">
                                    <ul class="menu">
                                        <li><a href="{{ route('courses.index') }}">Katalog Kursus</a></li>
                                        @auth
                                            <li><a href="{{ route('dashboard.index') }}">Dasbor Saya</a></li>
                                            <li><a href="{{ route('quizzes.history') }}">Riwayat Kuis</a></li>
                                        @else
                                            <li><a href="{{ route('register') }}">Daftar Akun</a></li>
                                            <li><a href="{{ route('login') }}">Masuk</a></li>
                                        @endauth
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-auto">
                            <div class="widget widget_nav_menu footer-widget">
                                <h3 class="widget_title">Kategori Populer</h3>
                                <div class="menu-all-pages-container">
                                    <ul class="menu">
                                        @foreach (\App\Models\Category::orderBy('name')->limit(5)->get() as $footerCategory)
                                            <li><a href="{{ route('courses.index', ['category' => $footerCategory->slug]) }}#katalog">{{ $footerCategory->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-auto">
                            <div class="widget footer-widget">
                                <div class="th-widget-contact style2">
                                    <h3 class="widget_title">Hubungi Kami</h3>
                                    <div class="info-box style2">
                                        <div class="box-details">
                                            <p class="box-text">Butuh bantuan?</p>
                                            <a href="mailto:halo@platformkursus.id" class="box-link">halo@platformkursus.id</a>
                                        </div>
                                    </div>
                                    <div class="info-box style2">
                                        <div class="box-details">
                                            <p class="box-text">Jam layanan</p>
                                            <span class="box-link">Senin–Sabtu, 08.00–20.00 WIB</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="copyright-wrap">
                <div class="container">
                    <div class="row gy-2 align-items-center">
                        <div class="col-lg-6">
                            <p class="copyright-text">&copy; {{ date('Y') }} Platform Kursus Online. Semua hak dilindungi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    {{-- Scroll to top --}}
    <div class="scroll-top">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>

    {{-- JS template Escul — dimuat lokal. three.js/hover-effect/th-cursor.js
         sengaja tidak dimuat (efek magic-cursor & displacement hover
         dekoratif, tidak dipakai markup di halaman ini). --}}
    <script src="{{ asset('escul/assets/js/vendor/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('escul/assets/js/plugin.min.js') }}"></script>
    <script src="{{ asset('escul/assets/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('escul/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('escul/assets/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('escul/assets/js/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('escul/assets/js/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('escul/assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('escul/assets/js/main.js') }}"></script>

    {{-- Alpine.js (bukan CSS Tailwind — cuma bagian JS-nya) dipakai untuk
         validasi form real-time di halaman masuk/daftar/kuis, supaya
         logic Alpine yang sudah ada tidak perlu ditulis ulang. --}}
    @vite(['resources/js/app.js'])

    @stack('scripts')
</body>

</html>
