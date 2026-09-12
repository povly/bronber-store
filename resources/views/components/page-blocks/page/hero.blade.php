@push('block-styles')
    @vite(['resources/css/blocks/page-blocks/style.css'])
@endpush

@php
    $image = $block['image'] ?? null;
    $src = null;
    if (!empty($image)) {
        $src = str_starts_with((string) $image, '/') || str_starts_with((string) $image, 'http')
            ? (string) $image
            : '/storage/'.ltrim((string) $image, '/');
    }
@endphp

<section class="pb-hero"{!! $src ? ' style="background-image: url(' . e($src) . ');"' : '' !!}>
    <div class="container">
        <div class="pb-hero__inner">
            @if(!empty($block['title']))
                <h1 class="pb-hero__title">{{ $block['title'] }}</h1>
            @endif
            @if(!empty($block['subtitle']))
                <p class="pb-hero__subtitle">{{ $block['subtitle'] }}</p>
            @endif
        </div>
    </div>
</section>
