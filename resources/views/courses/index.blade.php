@extends('layouts.escul')

@section('title', 'Platform Kursus Online — Belajar Online Lebih Terarah')

@section('content')
{{-- ============== Hero ============== --}}
<div class="th-hero-wrapper hero-5" id="hero">
    <div class="hero-inner">
        <div class="th-hero-bg" data-mask-src="{{ asset('escul/assets/img/hero/hero_bg_mask5_1.png') }}">
            <div class="thumb">
                <img class="img-cover" src="{{ asset('escul/assets/img/hero/hero_bg_5_1.jpg') }}" alt="Suasana belajar online">
            </div>
        </div>
        <div class="about-tag" data-ani="slideinup">
            <div class="about-experience-tag">
                <span class="circle-title-anime">{{ number_format($stats['courses'], 0, ',', '.') }} Kursus Siap Diikuti Hari Ini</span>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-xl-9">
                    <div class="hero-style5">
                        <h1 class="hero-title text-white">
                            <span class="title1" data-ani="slideinup" data-ani-delay="0.2s">Belajar Online Lebih Terarah, </span>
                            <span class="title2" data-ani="slideinup" data-ani-delay="0.4s">Fleksibel, dan </span>
                            <span class="title3 text-theme2 fw-normal" data-ani="slideinup" data-ani-delay="0.6s">Mudah Dipantau</span>
                        </h1>
                        <p class="hero-text text-white" data-ani="slideinup" data-ani-delay="0.7s">
                            Akses materi video, PDF, kuis, dan progres belajar dalam satu platform yang rapi untuk siswa dan pengelola kursus.
                        </p>
                        <div class="btn-wrap" data-ani="slideinup" data-ani-delay="0.8s">
                            <a href="{{ $anchor('katalog') }}" class="th-btn style2">LIHAT KATALOG KURSUS
                                <svg class="ms-2" width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.5264 0C7.5264 0.6962 8.21633 1.738 8.9138 2.61293C9.81193 3.7394 10.8838 4.72347 12.1137 5.4748C13.0351 6.0374 14.154 6.57747 15.0528 6.57747M7.5264 13.1712C7.5264 12.475 8.21633 11.4332 8.9138 10.5583C9.81193 9.43187 10.8838 8.44773 12.1137 7.6964C13.0351 7.1338 14.154 6.59373 15.0528 6.59373M15.0528 6.5856H0" stroke="currentColor" stroke-width="1.5"></path></svg>
                            </a>
                            @guest
                                <a href="{{ route('register') }}" class="nav-btn nav-btn-outline-light ms-2">DAFTAR GRATIS</a>
                            @endguest
                        </div>

                        <div class="hero-stats-wrap mt-5 d-flex flex-wrap gap-4" data-ani="slideinup" data-ani-delay="0.9s">
                            <div>
                                <p class="text-white-50 small mb-1">Kursus Aktif</p>
                                <p class="text-white fw-bold fs-3 mb-0">{{ number_format($stats['courses'], 0, ',', '.') }}+</p>
                            </div>
                            <div>
                                <p class="text-white-50 small mb-1">Kategori</p>
                                <p class="text-white fw-bold fs-3 mb-0">{{ $stats['categories'] }}</p>
                            </div>
                            <div>
                                <p class="text-white-50 small mb-1">Pelajar Terdaftar</p>
                                <p class="text-white fw-bold fs-3 mb-0">{{ number_format($stats['students'], 0, ',', '.') }}+</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============== Value proposition ============== --}}
<div class="space" id="about-sec">
    <div class="container">
        <div class="row gy-50 gx-80 align-items-center">
            <div class="col-xl-6 col-lg-10">
                <div class="img-box5 th_fade_anim" data-fade-from="left">
                    <div class="img1">
                        <div class="thumb"><img class="img-cover" src="{{ asset('escul/assets/img/normal/about_5_1.jpg') }}" alt="Pelajar belajar online dengan laptop"></div>
                    </div>
                    <div class="img2">
                        <div class="thumb"><img class="img-cover" src="{{ asset('escul/assets/img/normal/about_5_2.jpg') }}" alt="Diskusi belajar kelompok"></div>
                    </div>
                    <div class="about-tag">
                        <div class="year-counter">
                            <div class="box-title"><span class="counter-number">{{ $stats['categories'] }}</span></div>
                            <span class="small text-body">Kategori Kursus</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="about-wrap5">
                    <div class="title-area mb-35">
                        <span class="sub-title th_fade_anim"><img src="{{ asset('escul/assets/img/icon/subtitle-icon1-6.svg') }}" alt="">Kenapa Platform Kursus</span>
                        <h2 class="sec-title th_fade_anim" data-delay="0.15">Dibangun Supaya Kamu Benar-Benar Menyelesaikan Kursusmu</h2>
                        <p class="th_fade_anim" data-delay="0.25">Bukan cuma kumpulan video — setiap kursus disusun jadi modul dan pelajaran berurutan, dilengkapi kuis untuk menguji pemahamanmu, dan progresmu selalu tersimpan agar mudah dilanjutkan.</p>
                    </div>
                    <div class="about-info-wrap">
                        <div class="about-info-card th_fade_anim" data-delay="0.3">
                            <div class="box-icon"><img src="{{ asset('escul/assets/img/icon/about-card-icon5-1.svg') }}" alt="img"></div>
                            <div class="box-details">
                                <h3 class="box-title">Silabus Terstruktur</h3>
                                <p class="box-text">Modul & pelajaran berurutan</p>
                            </div>
                        </div>
                        <div class="about-info-card th_fade_anim" data-delay="0.4">
                            <div class="box-icon"><img src="{{ asset('escul/assets/img/icon/about-card-icon5-2.svg') }}" alt="img"></div>
                            <div class="box-details">
                                <h3 class="box-title">Progres Tersimpan</h3>
                                <p class="box-text">Lanjut dari terakhir kamu belajar</p>
                            </div>
                        </div>
                    </div>
                    <div class="btn-wrap mt-40 th_fade_anim" data-delay="0.45">
                        <a href="{{ $anchor('katalog') }}" class="th-btn style4">JELAJAHI KURSUS
                            <svg class="ms-2" width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.5264 0C7.5264 0.6962 8.21633 1.738 8.9138 2.61293C9.81193 3.7394 10.8838 4.72347 12.1137 5.4748C13.0351 6.0374 14.154 6.57747 15.0528 6.57747M7.5264 13.1712C7.5264 12.475 8.21633 11.4332 8.9138 10.5583C9.81193 9.43187 10.8838 8.44773 12.1137 7.6964C13.0351 7.1338 14.154 6.59373 15.0528 6.59373M15.0528 6.5856H0" stroke="currentColor" stroke-width="1.5"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============== Katalog kursus ============== --}}
<section class="space bg-smoke3" id="katalog" data-bg-src="{{ asset('escul/assets/img/bg/course-bg5-1.png') }}">
    <div class="container">
        <x-escul.section-title subtitle="Katalog Kursus">Pilih Kursus, Mulai Belajar Hari Ini</x-escul.section-title>

        <form method="GET" action="{{ route('courses.index') }}#katalog" class="row g-3 justify-content-center mb-50">
            <div class="col-md-6 col-lg-5">
                <label class="visually-hidden" for="search">Cari kursus</label>
                <input
                    type="search"
                    id="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari kursus berdasarkan judul atau deskripsi..."
                    class="form-control"
                >
            </div>
            <div class="col-md-4 col-lg-3">
                <label class="visually-hidden" for="category">Filter kategori</label>
                <select id="category" name="category" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="th-btn">CARI</button>
            </div>
        </form>

        <p class="text-center text-body mb-4">
            Menampilkan {{ $courses->count() }} dari {{ $courses->total() }} kursus
            @if ($search !== '' || $selectedCategory !== '')
                (difilter)
            @endif
        </p>

        <div class="row gy-4">
            @foreach ($courses as $course)
                <div class="col-md-6 col-lg-4 d-flex th_fade_anim" data-delay="{{ 0.15 + 0.1 * ($loop->index % 3) }}">
                    <x-escul.course-card :course="$course" class="w-100" />
                </div>
            @endforeach
        </div>

        @if ($courses->isEmpty())
            <div class="text-center text-body p-5 border border-dashed rounded-4 mt-4">
                @if ($search !== '' || $selectedCategory !== '')
                    Tidak ada kursus yang cocok dengan pencarian atau filter kamu.
                @else
                    Belum ada kursus yang tersedia.
                @endif
            </div>
        @endif

        @if ($courses->hasPages())
            <div class="mt-5 d-flex justify-content-center">
                {{ $courses->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
</section>

{{-- ============== Cara belajar ============== --}}
<div class="space" id="cara-belajar">
    <div class="container">
        <x-escul.section-title subtitle="Cara Belajar">Mulai Belajar Hanya dalam 4 Langkah</x-escul.section-title>

        <div class="row gy-4">
            @php
                $steps = [
                    ['icon' => 'fa-user-plus', 'title' => 'Daftar Akun', 'text' => 'Buat akun gratis dalam hitungan detik.'],
                    ['icon' => 'fa-magnifying-glass', 'title' => 'Pilih Kursus', 'text' => 'Cari kursus sesuai minat & levelmu.'],
                    ['icon' => 'fa-book-open-reader', 'title' => 'Ikuti Materi', 'text' => 'Pelajari video, teks, dan file pendukung tiap modul.'],
                    ['icon' => 'fa-clipboard-check', 'title' => 'Kerjakan Kuis', 'text' => 'Uji pemahamanmu & lihat progres di dasbor.'],
                ];
            @endphp
            @foreach ($steps as $step)
                <div class="col-sm-6 col-lg-3 th_fade_anim" data-delay="{{ 0.15 + 0.15 * $loop->index }}">
                    <div class="text-center px-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-theme text-white mb-3" style="width:64px;height:64px;font-size:22px;">
                            <i class="fal {{ $step['icon'] }}"></i>
                        </span>
                        <h3 class="h5">{{ $step['title'] }}</h3>
                        <p class="text-body small mb-0">{{ $step['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ============== Kenapa memilih kami (Keunggulan) ============== --}}
<section class="space overflow-hidden" id="keunggulan">
    <div class="container">
        <div class="row gy-40 align-items-center">
            <div class="col-xl-6">
                <div class="why-card-wrap5">
                    <div class="why-card5 th_fade_anim" data-delay="0.15">
                        <div class="box-icon"><i class="fal fa-chalkboard-user fs-2 text-theme"></i></div>
                        <h3 class="box-title">Instruktur <br>Berpengalaman</h3>
                        <p class="box-text">Materi disusun praktisi yang paham cara pemula belajar paling efektif.</p>
                    </div>
                    <div class="why-card5 th_fade_anim" data-delay="0.3">
                        <div class="box-icon"><i class="fal fa-clock fs-2 text-theme"></i></div>
                        <h3 class="box-title">Belajar Sesuai <br>Ritme</h3>
                        <p class="box-text">Tidak ada jadwal kaku — akses materi kapan pun kamu sempat.</p>
                    </div>
                    <div class="why-card5 th_fade_anim" data-delay="0.45">
                        <div class="box-icon"><i class="fal fa-list-check fs-2 text-theme"></i></div>
                        <h3 class="box-title">Kuis & <br>Pembahasan</h3>
                        <p class="box-text">Tiap modul ditutup kuis singkat lengkap pembahasan jawaban.</p>
                    </div>
                    <div class="why-card5 th_fade_anim" data-delay="0.6">
                        <div class="box-icon"><i class="fal fa-chart-line fs-2 text-theme"></i></div>
                        <h3 class="box-title">Progres yang <br>Jelas</h3>
                        <p class="box-text">Dasbor menunjukkan persis modul mana yang sudah selesai.</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="why-wrap5">
                    <div class="title-area mb-50">
                        <span class="sub-title th_fade_anim"><img src="{{ asset('escul/assets/img/icon/subtitle-icon1-6.svg') }}" alt="">Kenapa Belajar di Sini</span>
                        <h2 class="sec-title th_fade_anim" data-delay="0.15">Empat Alasan Pelajar Kami Bertahan Sampai Lulus</h2>
                        <p class="th_fade_anim" data-delay="0.25">Kami merancang setiap detail — dari cara materi disusun sampai bagaimana progresmu ditampilkan — supaya belajar online tidak terasa sepi atau membingungkan.</p>
                    </div>
                    <div class="btn-wrap th_fade_anim" data-delay="0.35">
                        <a href="{{ $anchor('katalog') }}" class="th-btn">JELAJAHI KURSUS
                            <svg class="ms-2" width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.5264 0C7.5264 0.6962 8.21633 1.738 8.9138 2.61293C9.81193 3.7394 10.8838 4.72347 12.1137 5.4748C13.0351 6.0374 14.154 6.57747 15.0528 6.57747M7.5264 13.1712C7.5264 12.475 8.21633 11.4332 8.9138 10.5583C9.81193 9.43187 10.8838 8.44773 12.1137 7.6964C13.0351 7.1338 14.154 6.59373 15.0528 6.59373M15.0528 6.5856H0" stroke="currentColor" stroke-width="1.5"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============== CTA ============== --}}
<section class="pt-80 pb-80 bg-theme overflow-hidden" data-bg-src="{{ asset('escul/assets/img/bg/cta-bg5-1.png') }}">
    <div class="container">
        <div class="row gy-40 justify-content-between align-items-center">
            <div class="col-xxl-6 col-xl-7 col-lg-7">
                <div class="title-area mb-0 text-lg-start text-center th_fade_anim">
                    <h2 class="sec-title text-white">Siap Mulai Belajar <span class="fw-normal text-theme2">Sesuatu yang Baru?</span></h2>
                    <p class="text-white mb-0 mt-30">Daftar gratis, jelajahi katalog kursus, dan mulai modul pertamamu hari ini juga.</p>
                </div>
            </div>
            <div class="col-lg-auto">
                <div class="btn-wrap justify-content-center th_fade_anim" data-delay="0.2">
                    @guest
                        <a href="{{ route('register') }}" class="th-btn style7">DAFTAR GRATIS
                            <svg class="ms-2" width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.5264 0C7.5264 0.6962 8.21633 1.738 8.9138 2.61293C9.81193 3.7394 10.8838 4.72347 12.1137 5.4748C13.0351 6.0374 14.154 6.57747 15.0528 6.57747M7.5264 13.1712C7.5264 12.475 8.21633 11.4332 8.9138 10.5583C9.81193 9.43187 10.8838 8.44773 12.1137 7.6964C13.0351 7.1338 14.154 6.59373 15.0528 6.59373M15.0528 6.5856H0" stroke="currentColor" stroke-width="1.5"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('dashboard.index') }}" class="th-btn style7">KE DASBOR SAYA</a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
