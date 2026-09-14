@push('block-styles')
    @vite(['resources/css/blocks/blog/style.css'])
@endpush

@push('block-scripts')
    @vite(['resources/js/blocks/blog/index.js'])
@endpush

@php
    // Cards are localized per the views rule: default locale → /blog/{slug},
    // others → /{locale}/blog/{slug} (default comes from LanguageService,
    // never hardcoded). MediaManagerPicker stores disk-relative paths —
    // absolute/URL paths pass through.
    $locale = app()->getLocale();
    $defaultLocale = resolve(\App\Services\Languages\LanguageService::class)->defaultCode();

    $normalize = static function (mixed $value): ?string {
        $path = trim((string) ($value ?? ''));

        if ($path === '') {
            return null;
        }

        return str_starts_with($path, '/') || str_starts_with($path, 'http')
            ? $path
            : '/storage/'.ltrim($path, '/');
    };
@endphp

<section class="blog" x-data="blog()">
    <div class="container">
        <h1 class="blog__title">{{ __('store.blog_title') }}</h1>

        <div class="blog__list" x-ref="list">
            @foreach ($articles as $article)
                @php
                    $translation = $article->translation();
                    $cover = $normalize($article->cover_pc) ?? $normalize($article->cover_mb);
                    $path = '/blog/'.$article->slug;
                    $href = $locale === $defaultLocale ? url($path) : url('/'.$locale.$path);
                @endphp
                <article class="blog__item" data-article>
                    <a href="{{ $href }}" class="article">
                        <x-img path="{{ $cover ?? '' }}" :alt="$translation?->title ?? ''" :lazy="false" class="article__image" width="464" height="650" />
                        <span class="article__shade article__shade--top"></span>
                        <span class="article__shade article__shade--bottom"></span>
                        <div class="article__top">
                            <span class="article__tag">{{ $translation?->tag }}</span>
                            <span class="article__date">{{ $article->published_at->format('d/m/y') }}</span>
                        </div>
                        <div class="article__body">
                            <h3 class="article__title">{{ $translation?->title }}</h3>
                            <p class="article__desc">{{ $translation?->excerpt }}</p>
                            <div class="article__reveal">
                                <span class="article__more btn btn--white">
                                    Детальнее
                                </span>
                            </div>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>

        <div class="blog__more" x-show="hasMore" x-cloak>
            <button type="button" class="blog__more-btn btn btn--primary" @click="showMore()">
                Показать больше
            </button>
        </div>
    </div>
</section>
