@push('block-styles')
    @once
        @vite(['resources/css/blocks/home/partners/style.css'])
    @endonce
@endpush

@php
    // MediaManagerPicker stores a list of disk-relative paths;
    // legacy Json format ({src: path}) is still accepted.
    $images = collect($block['images'] ?? [])
        ->map(static fn ($image): ?string => is_array($image) ? ($image['src'] ?? null) : (string) $image)
        ->filter()
        ->map(static function (string $path): string {
            return str_starts_with($path, '/') || str_starts_with($path, 'http')
                ? $path
                : '/storage/'.ltrim($path, '/');
        })
        ->values();
    $title = $block['title'] ?? 'Наши партнеры';
@endphp

@if($images->isNotEmpty())
    <section class="home-partners section">
        <div class="container">
            <x-slider
                :config="['breakpoints' => [0 => ['grid' => ['cols' => 2, 'rows' => 4]], 768 => ['grid' => ['cols' => 5, 'rows' => 2]]]]"
                class="home-partners__root"
                viewport-class="home-partners__slider"
                track-class="home-partners__track"
                label="{{ $title }}">
                <x-slot:header>
                    <div class="home-partners__header section__top">
                        <h2 class="home-partners__title section__title">{{ $title }}</h2>
                        <x-slider-arrows class="home-partners__arrows home-partners__arrows--pc slider__arrows--pc" />
                    </div>
                </x-slot:header>

                @foreach($images as $src)
                    <div class="home-partners__slide slider__slide">
                        <div class="home-partners__item">
                            <x-img path="{{ $src }}" width="150" height="38" />
                        </div>
                    </div>
                @endforeach
            </x-slider>
        </div>
    </section>
@endif
