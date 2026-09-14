@push('block-styles')
    @once
        @vite(['resources/css/blocks/article-page/style.css'])
    @endonce
@endpush

@extends('layouts.app')

@section('content')
    @php
        // Rendered inside the view (not in the controller) on purpose: block
        // views push their CSS/JS onto the "block-styles"/"block-scripts"
        // stacks, and pushes only survive to the layout when the block views
        // render nested inside another view — a controller-side ->render()
        // flushes the stacks before the layout can @stack them.
        $normalize = static function (mixed $value): ?string {
            $path = trim((string) ($value ?? ''));

            if ($path === '') {
                return null;
            }

            return str_starts_with($path, '/') || str_starts_with($path, 'http')
                ? $path
                : '/storage/'.ltrim($path, '/');
        };

        $coverPc = $normalize($article->cover_pc);
        $coverMb = $normalize($article->cover_mb) ?? $coverPc;
    @endphp
    <main class="article-page">
        @if (! empty($breadcrumbs))
            <div class="container">
                {{-- Flat utility class compiled from blocks/page/style.css (.page__breadcrumbs) --}}
                <x-breadcrumbs :items="$breadcrumbs" class="page__breadcrumbs article-page__breadcrumbs" />
            </div>
        @endif

        <section class="article-page-block">
            <div class="container">
                @if ($coverMb !== null || $coverPc !== null)
                    <div class="article-page-block__hero img--full">
                        @if ($coverMb !== null)
                            <x-img path="{{ $coverMb }}" alt="{{ $translation->title }}"
                                class="article-page-block__image" :lazy="false" width="330" height="225" />
                        @endif
                        @if ($coverPc !== null)
                            <x-img path="{{ $coverPc }}" alt="{{ $translation->title }}"
                                class="article-page-block__image" :lazy="false" width="718" height="308" />
                        @endif
                    </div>
                @endif

                <div class="article-page-block__body">
                    <p class="article-page-block__date">{{ $article->published_at->format('d/m/Y') }}</p>
                    <h1 class="article-page-block__title">{{ $translation->title }}</h1>
                </div>

                {!! resolve(\App\Support\PageBlocks\BlockRenderer::class)->render($translation->content ?? [], 'article') !!}
            </div>
        </section>
    </main>
@endsection
