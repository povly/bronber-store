@push('block-styles')
    @once
        @vite(['resources/css/blocks/delivery/style.css'])
    @endonce
@endpush

@php
    // Prototype markup of blocks/delivery/delivery.blade.php (contacts
    // part), data now comes from the flexible-layouts block. The href is
    // authored in the admin as a ready tel:/mailto:/https: URL — no
    // normalization here (unlike the prototype's preg_replace).
    $title = $block['title'] ?? '';
    $items = $block['items'] ?? [];
@endphp

<section class="delivery delivery--contacts">
    <div class="container">
        @if ($title !== '')
            <h2 class="delivery__contact-title section__title">{{ $title }}</h2>
        @endif

        <div class="delivery__contacts">
            @foreach ($items as $item)
                @php
                    $tag = empty($item['href']) ? 'div' : 'a';
                @endphp
                <{{ $tag }}@if (! empty($item['href'])) href="{{ $item['href'] }}" @endif class="delivery__contact">
                    @if (! empty($item['icon']))
                        <span class="delivery__contact-icon">
                            <img src="{{ $item['icon'] }}" alt="" loading="lazy">
                        </span>
                    @endif
                    {{ $item['text'] ?? '' }}
                </{{ $tag }}>
            @endforeach
        </div>
    </div>
</section>
