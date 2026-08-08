@extends('layouts.app')

@push('block-styles')
    @vite(['resources/css/blocks/profile/layout/style.css'])
@endpush

@section('content')
    <main class="profile-page">
        @include('blocks.profile.tabs.tabs')
        <div class="profile-page__right">
            @include('blocks.profile.orders.orders', ['mode' => 'full'])
        </div>
    </main>
@endsection
