@php
    // SetLocale middleware is route-level — it doesn't run on unmatched routes (404).
    // Detect locale from URL segment so the header/footer render in the right language.
    $localeSegment = request()->segment(1);
    if (in_array($localeSegment, config('app.available_locales', []), true)) {
        app()->setLocale($localeSegment);
    }
@endphp

@extends('layouts.app')

@section('content')
    <main class="error-404-page">
        @include('blocks.error-404.error-404')
    </main>
@endsection
