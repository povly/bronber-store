<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Мой сайт')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('head')
</head>
<body>

@yield('header')

@yield('content')

@yield('footer')

</body>
</html>
