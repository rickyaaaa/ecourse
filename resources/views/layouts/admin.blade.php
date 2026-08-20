<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin — Platform Kursus Online')</title>

    <link rel="stylesheet" href="{{ asset('wowdash/assets/css/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('wowdash/assets/css/lib/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('wowdash/assets/css/lib/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('wowdash/assets/css/style.css') }}">
</head>
<body>
    <aside class="sidebar">
        <button type="button" class="sidebar-close-btn">
            <i class="ri-close-line"></i>
        </button>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-logo d-flex align-items-center gap-2">
                <span class="d-flex justify-content-center align-items-center rounded-circle bg-primary-600 text-white flex-shrink-0" style="width:36px;height:36px;font-size:18px;">
                    <i class="ri-graduation-cap-fill"></i>
                </span>
                <span class="fw-bold text-lg">Platform Kursus</span>
            </a>
        </div>
        <div class="sidebar-menu-area">
            <ul class="sidebar-menu" id="sidebar-menu">
                @php
                    $adminNavItems = [
                        ['route' => 'admin.dashboard', 'label' => 'Dasbor', 'icon' => 'ri-dashboard-3-line'],
                        ['route' => 'admin.courses.index', 'label' => 'Kursus', 'icon' => 'ri-book-open-line'],
                        ['route' => 'admin.modules.index', 'label' => 'Materi & Pelajaran', 'icon' => 'ri-stack-line'],
                        ['route' => 'admin.quizzes.index', 'label' => 'Kuis', 'icon' => 'ri-file-list-3-line'],
                        ['route' => 'admin.participants.index', 'label' => 'Peserta', 'icon' => 'ri-team-line'],
                    ];
                @endphp

                @foreach ($adminNavItems as $item)
                    <li>
                        <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['route']) ? 'active-page' : '' }}">
                            <i class="{{ $item['icon'] }} menu-icon"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach

                <li class="sidebar-menu-group-title">Lainnya</li>
                <li>
                    <a href="{{ route('courses.index') }}">
                        <i class="ri-arrow-left-line menu-icon"></i>
                        <span>Kembali ke Situs</span>
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-100 text-start bg-transparent border-0 p-0">
                            <i class="ri-logout-box-r-line menu-icon"></i>
                            <span>Keluar</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </aside>

    <main class="dashboard-main">
        <div class="navbar-header">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto">
                    <div class="d-flex flex-wrap align-items-center gap-4">
                        <button type="button" class="sidebar-toggle">
                            <i class="ri-menu-line icon text-2xl non-active"></i>
                            <i class="ri-arrow-right-s-line icon text-2xl active"></i>
                        </button>
                        <button type="button" class="sidebar-mobile-toggle">
                            <i class="ri-menu-line icon"></i>
                        </button>
                        <h6 class="fw-semibold mb-0">@yield('page-title', 'Panel Admin')</h6>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        {{-- Notifikasi: demo/visual saja, belum ada sistem notifikasi sungguhan --}}
                        <div class="dropdown">
                            <button
                                class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
                                type="button" data-bs-toggle="dropdown"
                            >
                                <i class="ri-notification-3-line text-xl"></i>
                            </button>
                            <div class="dropdown-menu to-top dropdown-menu-sm p-16">
                                <h6 class="text-md fw-semibold mb-8">Notifikasi</h6>
                                <p class="text-sm text-secondary-light mb-0">Belum ada notifikasi baru.</p>
                            </div>
                        </div>

                        <div class="dropdown">
                            <button class="d-flex justify-content-center align-items-center rounded-circle" type="button" data-bs-toggle="dropdown">
                                <span class="w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle bg-primary-600 text-white fw-semibold">
                                    {{ Str::of(auth()->user()->name)->substr(0, 1)->upper() }}
                                </span>
                            </button>
                            <div class="dropdown-menu to-top dropdown-menu-sm">
                                <div class="py-12 px-16 radius-8 bg-primary-50 mb-16">
                                    <h6 class="text-lg text-primary-light fw-semibold mb-2">{{ auth()->user()->name }}</h6>
                                    <span class="text-secondary-light fw-medium text-sm">Admin</span>
                                </div>
                                <ul class="to-top-list">
                                    <li>
                                        <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3" href="{{ route('profile.edit') }}">
                                            <i class="ri-user-line icon text-xl"></i> Profil Saya
                                        </a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3 w-100 border-0 bg-transparent">
                                                <i class="ri-logout-box-r-line icon text-xl"></i> Keluar
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-main-body">
            @if (session('notice'))
                <div class="alert alert-success alert-dismissible fade show radius-8 d-flex align-items-center gap-2" role="alert">
                    <i class="ri-checkbox-circle-line"></i>
                    {{ session('notice') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show radius-8 d-flex align-items-center gap-2" role="alert">
                    <i class="ri-error-warning-line"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <footer class="d-footer">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto">
                    <p class="mb-0">&copy; {{ date('Y') }} Platform Kursus Online.</p>
                </div>
            </div>
        </footer>
    </main>

    <script src="{{ asset('wowdash/assets/js/lib/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('wowdash/assets/js/lib/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('wowdash/assets/js/lib/apexcharts.min.js') }}"></script>
    <script src="{{ asset('wowdash/assets/js/app.js') }}"></script>
    {{--
        Alpine.js (via resources/js/app.js) dipertahankan sementara untuk
        halaman Kelola Kursus/Materi/Kuis/Peserta yang masih pakai x-data
        (belum dimigrasikan ke jQuery/Bootstrap WowDash — itu tugas
        Phase 4-7). Hapus baris ini setelah semua halaman admin selesai
        dimigrasikan dan tidak ada lagi atribut x-data/x-show/x-model di
        resources/views/admin/**.
    --}}
    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>
