<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bronber Store')</title>
    @vite(['resources/css/app.css'])
    @stack('block-styles')
    @stack('head-scripts')
    @yield('head')
</head>
<body x-data="storeHeader(@js($searchTypes))"
      :class="{ 'overflow-hidden': $store.modal.stack.length }">

@include('blocks.common.header.header')
@include('blocks.common.mobile-menu.mobile-menu')
@include('blocks.common.mobile-nav.mobile-nav')

<div class="catalog-menu__overlay"
     x-show="$store.catalogMenu.isOpen"
     x-cloak
     @click="$store.catalogMenu.close()"
     x-transition:enter="catalog-menu__overlay--transition"
     x-transition:enter-start="catalog-menu__overlay--hidden"
     x-transition:leave="catalog-menu__overlay--transition"
     x-transition:leave-end="catalog-menu__overlay--hidden"></div>

@include('blocks.common.catalog-menu.catalog-menu')

@yield('content')

@include('blocks.common.footer.footer')

@vite(['resources/js/lazyload.js', 'resources/js/app.js'])
@stack('block-scripts')

</body>
</html>
