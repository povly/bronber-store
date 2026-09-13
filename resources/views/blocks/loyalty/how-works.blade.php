@push('block-styles')
    @once
        @vite(['resources/css/blocks/loyalty/how-works/style.css'])
    @endonce
@endpush

@php
    // Prototype markup of blocks/loyalty/how-works/how-works.blade.php,
    // data now comes from the flexible-layouts block. Icons are resolved
    // through <x-img> (inline SVG); MediaManagerPicker stores disk-relative
    // paths — absolute/URL paths pass through. Step text is EditorJS JSON
    // — rendered via RenderEditorJs (about-timeline pattern).
    $normalize = static fn (mixed $value): ?string => trim((string) ($value ?? '')) === ''
        ? null
        : (str_starts_with((string) $value, '/') || str_starts_with((string) $value, 'http')
            ? (string) $value
            : '/storage/'.ltrim((string) $value, '/'));

    $renderText = static fn (mixed $text): string => trim((string) ($text ?? '')) === ''
        ? ''
        : app(\Sckatik\MoonshineEditorJs\RenderEditorJs::class)->render((string) $text);

    $title = $block['title'] ?? '';

    $items = collect($block['items'] ?? [])
        ->filter(static fn ($item): bool => is_array($item))
        ->map(static function (array $item) use ($normalize): array {
            $item['icon'] = $normalize($item['icon'] ?? null);

            return $item;
        })
        ->values();
@endphp

<section class="loyalty-how section">
    <div class="container">
        @if (trim((string) $title) !== '')
            <h2 class="loyalty-how__title section__title">{{ $title }}</h2>
        @endif
        <div class="loyalty-how__steps">
            @foreach ($items as $item)
                <div class="loyalty-how__step">
                    @if (! empty($item['icon']))
                        <div class="loyalty-how__step-icon"><x-img path="{{ $item['icon'] }}" /></div>
                    @endif
                    <h3 class="loyalty-how__step-title">{!! $item['title'] ?? '' !!}</h3>
                    <div class="loyalty-how__step-text">{!! $renderText($item['text'] ?? null) !!}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
