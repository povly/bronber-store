@extends('layouts.app')


@section('content')
    <main class="catalog-page">
        <form method="GET" action="{{ route('catalog') }}" x-data="filters" @submit.prevent="submit($el)">
            @include('blocks.catalog.hero.hero')

            <div class="catalog-page__body container">
                @include('blocks.catalog.filters.filters')
                <div class="catalog-page__content">
                    @include('blocks.catalog.products.products')
                </div>
            </div>

            @include('blocks.catalog.pagination.pagination')
        </form>
    </main>
@endsection