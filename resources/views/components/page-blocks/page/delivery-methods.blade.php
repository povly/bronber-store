@push('block-styles')
    @once
        @vite(['resources/css/blocks/delivery/style.css'])
    @endonce
@endpush

@php
    // Prototype markup of blocks/delivery/delivery.blade.php (methods
    // part), data now comes from the flexible-layouts block. Icons are
    // media files (MediaManagerPicker) — <img> instead of inline <svg>.
    $title = $block['title'] ?? '';
    $items = $block['items'] ?? [];
@endphp

<section class="delivery">
    <div class="container">
        @if ($title !== '')
            <h1 class="delivery__title section__title">{{ $title }}</h1>
        @endif

        <div class="delivery__methods">
            @foreach ($items as $item)
                <div class="delivery__method">
                    @if (! empty($item['icon']))
                        <span class="delivery__icon">
                            <img src="{{ $item['icon'] }}" alt="" loading="lazy">
                        </span>
                    @endif
                    <h2 class="delivery__method-title">{!! nl2br(e($item['title'] ?? '')) !!}</h2>
                    <p class="delivery__method-text">{{ $item['text'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
