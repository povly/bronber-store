@php
    // Self-contained article card for the blog listing — used both by the
    // server-rendered first page and by the AJAX cards fragment
    // (blocks/blog/cards.blade.php). Card links are localized per the views
    // rule: default locale → /blog/{slug}, others → /{locale}/blog/{slug}.
    // MediaManagerPicker stores disk-relative paths — absolute/URL paths
    // pass through.
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

    $translation = $article->translation();
    $cover = $normalize($article->cover_pc) ?? $normalize($article->cover_mb);
    $path = '/blog/'.$article->slug;
    $href = $locale === $defaultLocale ? url($path) : url('/'.$locale.$path);
@endphp

<article class="blog__item">
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
                    {{ __('store.read_more') }}
                </span>
            </div>
        </div>
    </a>
</article>
