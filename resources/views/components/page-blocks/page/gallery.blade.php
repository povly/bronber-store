@push('block-styles')
    @vite(['resources/css/blocks/page-blocks/style.css'])
@endpush

@if(!empty($block['images']))
    <section class="pb-gallery">
        <div class="container">
            <div class="pb-gallery__grid">
                @foreach($block['images'] as $image)
                    @php
                        $src = $image['src'] ?? null;
                        if (!empty($src)) {
                            $src = str_starts_with((string) $src, '/') || str_starts_with((string) $src, 'http')
                                ? (string) $src
                                : '/storage/'.ltrim((string) $src, '/');
                        }
                    @endphp
                    @if(!empty($src))
                        <div class="pb-gallery__item">
                            <img src="{{ $src }}" alt="" loading="lazy" />
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif
