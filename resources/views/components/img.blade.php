@props(['class' => ''])

@if($found)
    @if($isSvg)
        {!! Str::replace(
            '<svg',
            '<svg' . ($class ? ' class="' . e(trim($class)) . '"' : ''),
            $svgContent
        ) !!}
    @else
        @php
            $isLazy = $lazy;
            $placeholderSvg = $placeholder ?? 'data:image/svg+xml,' . urlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . ($width ?? 1) . ' ' . ($height ?? 1) . '"><rect fill="%23f3f4f6" width="100%" height="100%"/></svg>');
        @endphp

        <img
            @if($isLazy) src="{{ $placeholderSvg }}" data-src="{{ asset($src) }}" @else src="{{ asset($src) }}" @endif
            @if($alt) alt="{{ $alt }}" @else alt="" @endif
            @if($width) width="{{ $width }}" @endif
            @if($height) height="{{ $height }}" @endif
            {{ $attributes->merge(['class' => trim(($isLazy ? 'lazy' : '') . ' ' . $class)]) }}
        />
    @endif
@endif
