<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Мой сайт')</title>
    @vite(['resources/css/app.css'])
    @stack('block-styles')
    @yield('head')
</head>
<body :class="{ 'overflow-hidden': $store.modal.stack.length }">

@include('common.header.header')

@yield('content')

@include('common.footer.footer')
@include('common.mobile-nav.mobile-nav')

@vite(['resources/js/lazyload.js', 'resources/js/app.js'])
@stack('block-scripts')

</body>
</html>
