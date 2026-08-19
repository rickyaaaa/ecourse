@props(['icon' => 'fa-chart-simple', 'label', 'value', 'accent' => 'theme'])

<div {{ $attributes->class(['stat-card']) }}>
    <div class="d-flex align-items-center gap-3">
        <span class="d-inline-flex align-items-center justify-content-center rounded-3 {{ $accent === 'theme2' ? 'bg-theme2' : 'bg-theme' }} text-white" style="width:44px;height:44px;">
            <i class="fal {{ $icon }}"></i>
        </span>
        <div>
            <p class="mb-0 text-body small">{{ $label }}</p>
            <p class="mb-0 fw-bold fs-4">{{ $value }}</p>
        </div>
    </div>
</div>
