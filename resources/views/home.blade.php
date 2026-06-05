@extends('layouts.app')

@push('block-styles')
    @vite(['resources/css/blocks/home/products/style.css'])
@endpush

@section('content')
    <main class="home-page">
        @include('blocks.home.hero')
        @include('blocks.home.categories')
        @include('blocks.home.advs')
        @include('blocks.home.products', ['title' => 'Рекомендованные товары'])
        @include('blocks.home.products', ['title' => 'Топливные насосы'])
        @include('blocks.home.products', ['title' => 'Тормозные диски'])
        @include('blocks.home.partners')
        @include('blocks.home.news')
    </main>
@endsection
