@php
    // MediaManagerPicker stores disk-relative paths — absolute/URL paths
    // pass through. Empty items were already resolved by MediaFallback.
    $normalize = static function (mixed $value): ?string {
        $path = trim((string) ($value ?? ''));

        if ($path === '') {
            return null;
        }

        return str_starts_with($path, '/') || str_starts_with($path, 'http')
            ? $path
            : '/storage/'.ltrim($path, '/');
    };

    $images = collect($block['items'] ?? [])
        ->map(static fn (array $item): ?string => $normalize($item['image'] ?? null))
        ->filter()
        ->values();
@endphp

@if ($images->isNotEmpty())
    <x-slider
        :config="['breakpoints' => [0 => ['perView' => 1], 768 => ['perView' => 2]]]"
        class="article-page-block__images"
        viewport-class="article-page-block__images-slider"
        track-class="article-page-block__images-track"
        label="Галерея">
        @foreach ($images as $image)
            <div class="article-page-block__image-wrap img--full slider__slide">
                <x-img path="{{ $image }}" alt=""
                    class="article-page-block__image" width="353" height="353" />
            </div>
        @endforeach

        <x-slot:nav>
            <x-slider-arrows class="article-page-block__images-arrows" />
        </x-slot:nav>
    </x-slider>
@endif
