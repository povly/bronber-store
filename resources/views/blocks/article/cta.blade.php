@php
    // Two-type link ({label, type, page, url}) resolved via LinkResolver:
    // broken links render no button at all.
    $label = trim((string) ($block['label'] ?? ''));
    $href = $label !== '' ? \App\Support\PageBlocks\LinkResolver::href($block) : null;
@endphp

@if ($href !== null)
    <div class="article-page-block__content article-page-block__content--cta">
        <a href="{{ $href }}" class="article-page-block__btn btn btn--primary">{{ $label }}</a>
    </div>
@endif
