@props([
    'items' => [],
    'class' => '',
])

@php
    $breadcrumbsItems = is_array($items) ? $items : [];

    // Build SEO JSON-LD BreadcrumbList (https://schema.org/BreadcrumbList)
    $jsonLdItems = [];
    $itemsCount = count($breadcrumbsItems);
    foreach ($breadcrumbsItems as $index => $item) {
        $listItem = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $item['label'] ?? '',
        ];

        // Resolve URL: explicit one, or current page for the last (current) item
        $url = $item['url'] ?? null;
        if ($url === null && $index === $itemsCount - 1) {
            $listItem['item'] = request()->url();
        } elseif ($url !== null) {
            $listItem['item'] = url($url);
        }

        $jsonLdItems[] = $listItem;
    }

    $jsonLd = $jsonLdItems ? [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $jsonLdItems,
    ] : null;
@endphp

<nav {{ $attributes->merge(['class' => "breadcrumbs {$class}"]) }} aria-label="Breadcrumb">
    <ul class="breadcrumbs__list">
        @foreach($breadcrumbsItems as $item)
            <li class="breadcrumbs__item">
                @if(!empty($item['url']) && !$loop->last)
                    <a href="{{ $item['url'] }}" class="breadcrumbs__link">{{ $item['label'] ?? '' }}</a>
                @else
                    <span class="breadcrumbs__current">{{ $item['label'] ?? '' }}</span>
                @endif
            </li>
        @endforeach
    </ul>
</nav>

@push('head-scripts')
    @if($jsonLd)
    <script type="application/ld+json">
{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_PRETTY_PRINT) !!}
    </script>
    @endif
@endpush
