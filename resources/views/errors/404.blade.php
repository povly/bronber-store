@php
    // SetLocale middleware is route-level — it doesn't run on unmatched routes (404).
    // Detect locale from URL segment so the header/footer render in the right language.
    $localeSegment = request()->segment(1);
    if (in_array($localeSegment, config('app.available_locales', []), true)) {
        app()->setLocale($localeSegment);
    }

    // 404 content comes from the error-404 setting (key × locale, ru
    // fallback inside SettingService). An empty setting keeps the static
    // prototype include — same fallback philosophy as header/footer.
    $errorBlocks = resolve(\App\Services\Settings\SettingService::class)->get('error-404');
@endphp

@extends('layouts.app')

@section('content')
    <main class="error-404-page">
        @if ($errorBlocks !== [])
            {!! resolve(\App\Support\PageBlocks\BlockRenderer::class)->render($errorBlocks, 'error-404') !!}
        @else
            @include('blocks.error-404.error-404')
        @endif
    </main>
@endsection
