@props(['subtitle' => null, 'align' => 'center'])

<div class="title-area {{ $align === 'center' ? 'text-center' : '' }} mb-4">
    @if ($subtitle)
        <span class="sub-title"><img src="{{ asset('escul/assets/img/icon/subtitle-icon1-6.svg') }}" alt="">{{ $subtitle }}</span>
    @endif
    <h2 class="sec-title">{{ $slot }}</h2>
</div>
