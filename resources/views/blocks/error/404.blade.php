@push('block-styles')
    @once
        @vite(['resources/css/blocks/error-404/style.css'])
    @endonce
@endpush

@php
    // Prototype markup of blocks/error-404/error-404.blade.php, data now
    // comes from the error-404 setting block. Buttons resolve their href
    // through LinkResolver ({label, type: page|custom, page, url}) —
    // broken links are skipped, an empty variant falls back to primary.
    $title = $block['title'] ?? '';
    $text = $block['text'] ?? '';

    $buttons = collect($block['buttons'] ?? [])
        ->filter(static fn ($button): bool => is_array($button))
        ->map(static function (array $button): array {
            $button['label'] = trim((string) ($button['label'] ?? ''));
            $button['variant'] = trim((string) ($button['variant'] ?? '')) ?: 'primary';

            return $button;
        })
        ->filter(static fn (array $button): bool => $button['label'] !== '')
        ->values();
@endphp

<section class="error-404">
    <div class="container">
        <div class="error-404__inner">
            <div class="error-404__code">404</div>
            <h1 class="error-404__title section__title">{{ $title }}</h1>
            <p class="error-404__desc">{!! $text !!}</p>
            <div class="error-404__actions">
                @foreach ($buttons as $button)
                    @php $href = \App\Support\PageBlocks\LinkResolver::href($button); @endphp
                    @if ($href !== null)
                        <x-btn variant="{{ $button['variant'] }} error-404__btn" href="{{ $href }}" :text="$button['label']" />
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
