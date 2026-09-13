@push('block-styles')
    @once
        @vite(['resources/css/blocks/delivery/style.css'])
    @endonce
@endpush

@php
    // Prototype markup of blocks/delivery/delivery.blade.php (methods
    // part), data now comes from the flexible-layouts block. Icons are
    // resolved through <x-img> (inline SVG); MediaManagerPicker stores
    // disk-relative paths — absolute/URL paths pass through.
    $title = $block['title'] ?? '';
    $items = collect($block['items'] ?? [])
        ->filter(static fn ($item): bool => is_array($item))
        ->map(static function (array $item): array {
            $icon = $item['icon'] ?? null;

            if (! empty($icon)) {
                $item['icon'] = str_starts_with((string) $icon, '/') || str_starts_with((string) $icon, 'http')
                    ? (string) $icon
                    : '/storage/'.ltrim((string) $icon, '/');
            }

            return $item;
        })
        ->values();
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
                            <x-img path="{{ $item['icon'] }}" />
                        </span>
                    @endif
                    <h2 class="delivery__method-title">{!! nl2br(e($item['title'] ?? '')) !!}</h2>
                    <p class="delivery__method-text">{{ $item['text'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
