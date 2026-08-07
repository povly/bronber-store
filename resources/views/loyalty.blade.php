@extends('layouts.app')

@section('content')
    <main class="loyalty-page">
        @include('blocks.loyalty.hero.hero')
        @include('blocks.loyalty.benefits.benefits')
        @include('blocks.loyalty.how-works.how-works')
        @include('blocks.loyalty.bottom.bottom')
    </main>
@endsection
