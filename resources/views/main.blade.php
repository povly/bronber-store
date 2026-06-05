@extends('layouts.app')

@section('content')
    <main class="catalog-page">
        <form method="GET" action="{{ route('catalog') }}" x-data="filters" @submit.prevent="submit($el)">
            @include('catalog.hero.hero')

            <div class="catalog-page__body container">
                @include('catalog.filters.filters')
                <div class="catalog-page__content">
                    @include('catalog.products.products')
                    @include('catalog.pagination.pagination')
                </div>
            </div>
        </form>
    </main>
@endsection