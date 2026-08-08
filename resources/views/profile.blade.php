@extends('layouts.app')

@push('block-styles')
    @vite(['resources/css/blocks/profile/layout/style.css'])
@endpush

@section('content')
    <main class="profile-page">
        @include('blocks.profile.tabs.tabs')
        @include('blocks.profile.summary.summary')
        @include('blocks.profile.stats.stats')
        @include('blocks.profile.orders.orders')
    </main>
@endsection
