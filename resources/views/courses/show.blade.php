@extends('layouts.escul')

@section('title', $course['title'] . ' — Platform Kursus Online')
@section('meta_description', Str::limit(strip_tags($course['description']), 150))

@section('content')
<x-escul.breadcrumb :title="$course['title']" :items="[$course['category'] => null]" />

<section class="space-top space-extra-bottom overflow-hidden">
    <div class="container">
        <div class="row gx-40 gy-4">
            <div class="col-xxl-8 col-lg-7">
                <div class="course-single mb-30">
                    <div class="course-single-top">
                        <div class="course-img">
                            <div class="course-thumb-placeholder bg-gradient {{ $course['thumbnail_color'] }} text-white" style="height:280px;border-radius:12px;">
                                <span class="display-3" aria-hidden="true">{{ $course['thumbnail_icon'] }}</span>
                            </div>
                        </div>
                        <h1 class="course-title">{{ $course['title'] }}</h1>

                        <div class="box-content">
                            <div class="course-info">
                                <div class="box-icon"><i class="fal fa-tag"></i></div>
                                <div class="course-info-details">
                                    <span class="course-info-title">Kategori:</span>
                                    <h4 class="course-info-text">{{ $course['category'] }}</h4>
                                </div>
                            </div>
                            @if (! empty($course['level']))
                                <div class="course-info">
                                    <div class="box-icon"><i class="fal fa-signal-bars"></i></div>
                                    <div class="course-info-details">
                                        <span class="course-info-title">Level:</span>
                                        <h4 class="course-info-text">{{ $course['level'] }}</h4>
                                    </div>
                                </div>
                            @endif
                            <div class="course-info">
                                <div class="box-icon"><i class="fal fa-users"></i></div>
                                <div class="course-info-details">
                                    <span class="course-info-title">Pelajar:</span>
                                    <h4 class="course-info-text">{{ number_format($course['students_count'], 0, ',', '.') }}+</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="course-single-bottom">
                        <ul class="nav course-tab" id="courseTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="description-tab" data-bs-toggle="tab" href="#Coursedescription" role="tab" aria-controls="Coursedescription" aria-selected="true"><i class="fa-regular fa-bookmark"></i>Ringkasan</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="curriculam-tab" data-bs-toggle="tab" href="#curriculam" role="tab" aria-controls="curriculam" aria-selected="false"><i class="fa-regular fa-book"></i>Silabus</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="courseTabContent">
                            <div class="tab-pane fade show active" id="Coursedescription" role="tabpanel" aria-labelledby="description-tab">
                                <div class="course-description">
                                    <h5 class="h5 mb-4">Tentang Kursus Ini</h5>
                                    @if (! empty($course['description']))
                                        <p>{{ $course['description'] }}</p>
                                    @else
                                        <p class="text-body">Belum ada deskripsi untuk kursus ini.</p>
                                    @endif

                                    <div class="row gy-4 mt-2">
                                        <div class="col-lg-6">
                                            <div class="stat-card text-center">
                                                <p class="mb-0 fw-bold fs-3">{{ $course['modules_count'] }}</p>
                                                <p class="mb-0 text-body small">Modul</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="stat-card text-center">
                                                <p class="mb-0 fw-bold fs-3">{{ $course['lessons_count'] }}</p>
                                                <p class="mb-0 text-body small">Pelajaran</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="curriculam" role="tabpanel" aria-labelledby="curriculam-tab">
                                <div class="course-curriculam">
                                    <h5 class="h5 mb-3">Silabus Kursus</h5>

                                    <div class="accordion" id="syllabusAccordion">
                                        @forelse ($syllabus as $index => $module)
                                            <div class="accordion-card style2">
                                                <div class="accordion-header" id="syllabus-heading-{{ $module['id'] }}">
                                                    <button
                                                        class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}"
                                                        type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#syllabus-collapse-{{ $module['id'] }}"
                                                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                        aria-controls="syllabus-collapse-{{ $module['id'] }}"
                                                    >
                                                        {{ $module['title'] }}
                                                        <span class="text-body small fw-normal ms-2">({{ count($module['lessons']) }} pelajaran)</span>
                                                    </button>
                                                </div>
                                                <div
                                                    id="syllabus-collapse-{{ $module['id'] }}"
                                                    class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                                    aria-labelledby="syllabus-heading-{{ $module['id'] }}"
                                                    data-bs-parent="#syllabusAccordion"
                                                >
                                                    <div class="accordion-body pt-0">
                                                        <ul class="list-unstyled mb-0">
                                                            @foreach ($module['lessons'] as $lesson)
                                                                <li class="border-top py-2">
                                                                    <a href="{{ route('lessons.show', [$course['slug'], $lesson['id']]) }}" class="text-inherit d-flex align-items-center gap-2">
                                                                        <i class="fal {{ $lesson['type'] === 'video' ? 'fa-circle-play' : 'fa-file-lines' }} text-theme"></i>
                                                                        {{ $lesson['title'] }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                            <li class="border-top py-2">
                                                                <a href="{{ route('quizzes.show', [$course['slug'], $module['id']]) }}" class="text-inherit d-flex align-items-center gap-2 fw-semibold" style="color:var(--theme-color2);">
                                                                    <i class="fal fa-clipboard-check"></i>
                                                                    Kerjakan Kuis Modul Ini
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-body">Silabus kursus ini belum tersedia.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-4 col-lg-5">
                <aside class="sidebar-area pt-0">
                    <div class="widget widget_info widget_course_info" style="position:sticky;top:100px;">
                        <h3 class="widget_title">Ikuti Kursus Ini</h3>

                        <x-enroll-status :enrollment="$course['enrollment']" :course="$course['slug']" :continue-url="$course['continue_url']" />

                        <h3 class="widget_title mt-30">Informasi Kursus</h3>
                        <div class="info-list">
                            <ul>
                                <li>
                                    <i class="fa-light fa-tag"></i>
                                    <strong>Kategori: </strong>
                                    <span>{{ $course['category'] }}</span>
                                </li>
                                @if (! empty($course['level']))
                                    <li>
                                        <i class="fa-light fa-signal-bars"></i>
                                        <strong>Level: </strong>
                                        <span>{{ $course['level'] }}</span>
                                    </li>
                                @endif
                                <li>
                                    <i class="fa-light fa-file"></i>
                                    <strong>Modul: </strong>
                                    <span>{{ $course['modules_count'] }}</span>
                                </li>
                                <li>
                                    <i class="fa-light fa-book-open"></i>
                                    <strong>Pelajaran: </strong>
                                    <span>{{ $course['lessons_count'] }}</span>
                                </li>
                                <li>
                                    <i class="fal fa-users"></i>
                                    <strong>Pelajar: </strong>
                                    <span>{{ number_format($course['students_count'], 0, ',', '.') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>
@endsection
