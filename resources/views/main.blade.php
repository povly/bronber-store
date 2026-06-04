@extends('layouts.app')

@section('header')
    @include('common.top-bar.top-bar')
    @include('common.header.header')
@endsection

@section('content')
    <main class="catalog-page">
        <form method="GET" action="{{ route('catalog') }}" @submit.prevent="submitFilters($event)">
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

@section('footer')
    @include('common.footer.footer')
    @include('common.mobile-nav.mobile-nav')
@endsection