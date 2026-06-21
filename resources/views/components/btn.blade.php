@props(['class' => ''])

@php
    $tag = $href ? 'a' : 'button';
    $attrs = $tag === 'a' ? 'href="' . e($href) . '"' : 'type="button"';
@endphp

<{{ $tag }} {!! $attrs !!} {{ $attributes->merge(['class' => "btn btn--{$variant} {$class}"]) }}>
    @if ($icon)
        <span class="btn__icon">{!! $icon !!}</span>
    @endif
    @if ($text)
        <span class="btn__text">{{ $text }}</span>
    @endif
    {{ $slot }}
</{{ $tag }}>
