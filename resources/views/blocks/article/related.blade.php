@php
    // Cards are localized per the views rule: default locale → /blog/{slug},
    // others → /{locale}/blog/{slug} (default comes from LanguageService,
    // never hardcoded).
    $locale = app()->getLocale();
    $defaultLocale = resolve(\App\Services\Languages\LanguageService::class)->defaultCode();

    $articleUrl = static function (\App\Models\Article $item) use ($locale, $defaultLocale): string {
        $path = '/blog/'.$item->slug;

        return $locale === $defaultLocale ? url($path) : url('/'.$locale.$path);
    };

    $normalize = static function (mixed $value): ?string {
        $path = trim((string) ($value ?? ''));

        if ($path === '') {
            return null;
        }

        return str_starts_with($path, '/') || str_starts_with($path, 'http')
            ? $path
            : '/storage/'.ltrim($path, '/');
    };

    $title = trim((string) ($block['title'] ?? '')) ?: 'Другие новости';
@endphp

@if (! empty($related))
    <x-slider
        :config="['breakpoints' => [0 => ['perView' => 1], 768 => ['perView' => 2], 1200 => ['perView' => 3]]]"
        class="article-page-block__related section"
        viewport-class="article-page-block__related-slider"
        track-class="article-page-block__related-track"
        label="{{ $title }}">
        <x-slot:header>
            <div class="article-page-block__related-header section__top">
                <h2 class="article-page-block__related-title section__title">{{ $title }}</h2>
                <x-slider-arrows class="article-page-block__related-arrows article-page-block__related-arrows--top" />
            </div>
        </x-slot:header>

        @foreach ($related as $item)
            @php
                $translation = $item->translation();
                $cover = $normalize($item->cover_pc) ?? $normalize($item->cover_mb);
            @endphp
            <article class="article-page-block__related-slide slider__slide">
                <a href="{{ $articleUrl($item) }}" class="article">
                    <x-img path="{{ $cover ?? '' }}" alt="{{ $translation?->title ?? '' }}"
                        class="article__image" width="464" height="650" />
                    <span class="article__shade article__shade--top"></span>
                    <span class="article__shade article__shade--bottom"></span>
                    <div class="article__top">
                        <span class="article__tag">{{ $translation?->tag }}</span>
                        <span class="article__date">{{ $item->published_at->format('d/m/y') }}</span>
                    </div>
                    <div class="article__body">
                        <h3 class="article__title">{{ $translation?->title }}</h3>
                        <p class="article__desc">{{ $translation?->excerpt }}</p>
                        <div class="article__reveal">
                            <span class="article__more btn btn--white">{{ __('store.read_more') }}</span>
                        </div>
                    </div>
                </a>
            </article>
        @endforeach

        <x-slot:nav>
            <x-slider-arrows class="article-page-block__related-arrows article-page-block__related-arrows--bottom" />
        </x-slot:nav>
    </x-slider>
@endif
