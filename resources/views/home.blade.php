@extends('layouts.app')

@section('content')
    <main class="home-page">
        @include('blocks.home.hero')
        @include('blocks.home.categories')
        @include('blocks.home.advs')
        @include('blocks.home.products', ['title' => 'Рекомендованные товары'])
        @include('blocks.home.products', ['title' => 'Топливные насосы'])
        @include('blocks.home.products', ['title' => 'Тормозные диски'])
        @include('blocks.home.partners.partners')
        @include('blocks.home.news.news')
    </main>
@endsection
