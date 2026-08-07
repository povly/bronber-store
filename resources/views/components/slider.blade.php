@props([
    'config' => [],
    'id' => '',
    'label' => '',
])

@php
    $rootClass = $attributes->get('class', '');
    $viewportClass = $attributes->get('viewportClass', $attributes->get('viewport-class', ''));
    $trackClass = $attributes->get('trackClass', $attributes->get('track-class', ''));
    $defaultArrows = $attributes->has('default-arrows')
        ? filter_var($attributes->get('default-arrows'), FILTER_VALIDATE_BOOLEAN)
        : (bool) $attributes->get('defaultArrows', false);
@endphp

<div @if($id) id="{{ $id }}" @endif class="{{ $rootClass }}"
    x-data='slider(@json($config))' @resize.window.debounce.150ms="onResize()">

    <div aria-live="polite" aria-atomic="true"
        style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap"
        x-text="'Страница ' + (currentPage + 1) + ' из ' + totalPages"></div>

    @isset($header)
        {{ $header }}
    @endisset

    <div class="slider {{ $viewportClass }}" role="region" aria-roledescription="карусель"
        aria-label="{{ $label }}" tabindex="0"
        @keydown.arrow-left.prevent="prev()" @keydown.arrow-right.prevent="next()">
        <div class="slider__track {{ $trackClass }}" x-ref="track"
            @pointerdown.prevent="onPointerDown($event)"
            @pointermove.window="onPointerMove($event)"
            @pointerup.window="onPointerUp()"
            @pointercancel.window="onPointerUp()"
            @click.capture="suppressDragClick($event)">
            {{ $slot }}
        </div>
    </div>

    @isset($nav)
        {{ $nav }}
    @elseif($defaultArrows)
        <x-slider-arrows />
    @endisset
</div>
