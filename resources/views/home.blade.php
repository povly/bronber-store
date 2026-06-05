@extends('layouts.app')

@section('content')
    <main class="home-page">
        @include('blocks.home.hero')
        @include('home.categories.categories')
        @include('home.products.products', ['title' => 'Рекомендованные товары'])
        @include('home.products.products', ['title' => 'Топливные насосы'])
        @include('home.products.products', ['title' => 'Тормозные диски'])
        @include('home.partners.partners')
        @include('home.news.news')
    </main>
@endsection
