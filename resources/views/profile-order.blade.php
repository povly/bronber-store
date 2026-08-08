@extends('layouts.app')

@push('block-styles')
    @vite(['resources/css/blocks/profile/layout/style.css'])
@endpush

@section('content')
    <main class="profile-page">
        @include('blocks.profile.tabs.tabs')
        <div class="profile-page__right">
            @include('blocks.profile.order.order')
        </div>
    </main>
@endsection
