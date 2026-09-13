@push('block-styles')
    @once
        @vite(['resources/css/blocks/loyalty/benefits/style.css'])
    @endonce
@endpush

@php
    // Prototype markup of blocks/loyalty/benefits/benefits.blade.php,
    // data now comes from the flexible-layouts block. An item with a
    // filled title becomes the «main» one (big icon + title, modifier
    // --main); others render icon + text only. Text is EditorJS JSON
    // — rendered via RenderEditorJs (about-timeline pattern).
    $normalize = static fn (mixed $value): ?string => trim((string) ($value ?? '')) === ''
        ? null
        : (str_starts_with((string) $value, '/') || str_starts_with((string) $value, 'http')
            ? (string) $value
            : '/storage/'.ltrim((string) $value, '/'));

    $renderText = static fn (mixed $text): string => trim((string) ($text ?? '')) === ''
        ? ''
        : app(\Sckatik\MoonshineEditorJs\RenderEditorJs::class)->render((string) $text);

    $items = collect($block['items'] ?? [])
        ->filter(static fn ($item): bool => is_array($item))
        ->map(static function (array $item) use ($normalize): array {
            $item['icon'] = $normalize($item['icon'] ?? null);
            $item['title'] = trim((string) ($item['title'] ?? ''));

            return $item;
        })
        ->values();
@endphp

<section class="loyalty-benefits section">
    <div class="container">
        <div class="loyalty-benefits__list">
            @foreach ($items as $item)
                <div class="loyalty-benefits__item{{ $item['title'] !== '' ? ' loyalty-benefits__item--main' : '' }}">
                    @if (! empty($item['icon']))
                        <span class="loyalty-benefits__icon"><x-img path="{{ $item['icon'] }}" /></span>
                    @endif
                    @if ($item['title'] !== '')
                        <div class="loyalty-benefits__body">
                            <div class="loyalty-benefits__title">{{ $item['title'] }}</div>
                            <div class="loyalty-benefits__text">{!! $renderText($item['text'] ?? null) !!}</div>
                        </div>
                    @else
                        <div class="loyalty-benefits__text">{!! $renderText($item['text'] ?? null) !!}</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
