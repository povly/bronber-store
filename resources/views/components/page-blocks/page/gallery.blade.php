@push('block-styles')
    @vite(['resources/css/blocks/page-blocks/style.css'])
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
        ->values()
        ->all();
@endphp

@if($images !== [])
    <section class="pb-gallery">
        <div class="container">
            <div class="pb-gallery__grid">
                @foreach($images as $src)
                    <div class="pb-gallery__item">
                        <img src="{{ $src }}" alt="" loading="lazy" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
