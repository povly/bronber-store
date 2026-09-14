@push('block-styles')
    @vite(['resources/css/blocks/blog/style.css'])
@endpush

@push('block-scripts')
    @vite(['resources/js/blocks/blog/index.js'])
@endpush

@php
    // The first page (3 cards) renders server-side; «Показать больше»
    // appends the next pages over AJAX from the cards endpoint.
    $defaultLocale = resolve(\App\Services\Languages\LanguageService::class)->defaultCode();
    $cardsUrl = app()->getLocale() === $defaultLocale
        ? url('/blog/cards')
        : url('/'.app()->getLocale().'/blog/cards');
@endphp

<section class="blog" x-data="blog({ hasMore: @json($articles->hasMorePages()), cardsUrl: '{{ $cardsUrl }}' })">
    <div class="container">
        <h1 class="blog__title">{{ __('store.blog_title') }}</h1>

        <div class="blog__list" x-ref="list">
            @foreach ($articles as $article)
                @include('blocks.blog.card', ['article' => $article])
            @endforeach
        </div>

        <div class="blog__more" x-show="hasMore" x-cloak>
            <button type="button" class="blog__more-btn btn btn--primary" @click="showMore()" :disabled="loading">
                {{ __('store.blog_show_more') }}
            </button>
        </div>
    </div>
</section>
