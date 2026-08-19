@props(['title', 'items' => []])

<div class="breadcumb-wrapper" data-bg-src="{{ asset('escul/assets/img/bg/breadcumb-bg.png') }}">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">{{ $title }}</h1>
            <ul class="breadcumb-menu">
                <li><a href="{{ route('courses.index') }}">Beranda</a></li>
                @foreach ($items as $label => $url)
                    @if ($url)
                        <li><a href="{{ $url }}">{{ $label }}</a></li>
                    @else
                        <li>{{ $label }}</li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</div>
