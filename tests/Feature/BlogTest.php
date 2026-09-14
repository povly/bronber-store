<?php

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\Language;
use App\Services\Languages\LanguageService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);
});

/**
 * Article blocks for the demo translation (article-* types).
 *
 * @return list<array<string, mixed>>
 */
function articleBlocks(string $locale): array
{
    return [
        [
            '_type' => 'article-content',
            'text' => (string) json_encode([
                'time' => 0,
                'blocks' => [
                    ['type' => 'paragraph', 'data' => ['text' => $locale === 'ru' ? 'Тестовый лид статьи' : 'Test article lead']],
                ],
                'version' => '1.0.0',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ],
        [
            '_type' => 'article-gallery',
            'items' => [
                ['image' => '/images/blog/1.jpg'],
                ['image' => '/images/blog/2.jpg'],
            ],
        ],
        [
            '_type' => 'article-cta',
            'label' => $locale === 'ru' ? 'Перейти к направлению' : 'Go to division',
            'type' => 'custom',
            'page' => null,
            'url' => '/about',
        ],
        [
            '_type' => 'article-related',
            'title' => $locale === 'ru' ? 'Другие новости' : 'Other news',
        ],
    ];
}

function blogArticle(string $slug = 'zapusk-bronber-auto-service'): Article
{
    return Article::factory()
        ->has(ArticleTranslation::factory()->ru()->state([
            'title' => 'Запуск Bronber Auto Service',
            'tag' => '#запуск',
            'excerpt' => 'Профессиональный автосервис как часть экосистемы',
            'meta_title' => 'Запуск — Bronber',
            'meta_description' => 'Описание статьи для SEO',
            'content' => articleBlocks('ru'),
        ]), 'translations')
        ->has(ArticleTranslation::factory()->en()->state([
            'title' => 'Launch of Bronber Auto Service',
            'tag' => '#launch',
            'excerpt' => 'A professional auto service',
            'content' => articleBlocks('en'),
        ]), 'translations')
        ->create([
            'slug' => $slug,
            'published_at' => '2025-12-25',
            'cover_pc' => '/images/blog/1.jpg',
            'cover_mb' => '/images/blog/2.jpg',
        ]);
}

it('renders the blog listing from db articles', function (): void {
    blogArticle();

    $this->get('/blog')
        ->assertOk()
        ->assertSee('Запуск Bronber Auto Service')
        ->assertSee('#запуск')
        ->assertSee('25/12/25')
        ->assertSee('Профессиональный автосервис как часть экосистемы');
});

it('renders only the first page and serves the rest over the cards endpoint', function (): void {
    foreach (['2025-12-25', '2025-12-18', '2025-12-10', '2025-12-01'] as $index => $date) {
        Article::factory()
            ->has(ArticleTranslation::factory()->ru()->state([
                'title' => 'Статья номер '.$index,
            ]), 'translations')
            ->create(['slug' => 'article-'.$index, 'published_at' => $date]);
    }

    $this->get('/blog')
        ->assertOk()
        ->assertSee('Статья номер 0')
        ->assertSee('Статья номер 1')
        ->assertSee('Статья номер 2')
        ->assertDontSee('Статья номер 3');

    $response = $this->getJson('/blog/cards?page=2')->assertOk();

    expect($response->json('html'))->toContain('Статья номер 3')
        ->and($response->json('has_more'))->toBeFalse();
});

it('reports more pages from the cards endpoint while batches remain', function (): void {
    Article::factory()
        ->count(7)
        ->sequence(fn ($sequence) => ['slug' => 'batch-article-'.$sequence->index, 'published_at' => '2025-12-'.str_pad((string) (25 - $sequence->index), 2, '0', STR_PAD_LEFT)])
        ->has(ArticleTranslation::factory()->ru(), 'translations')
        ->create();

    $response = $this->getJson('/blog/cards?page=2')->assertOk();

    expect($response->json('has_more'))->toBeTrue()
        ->and(substr_count((string) $response->json('html'), 'blog__item'))->toBe(3);
});

it('renders en cards on the locale-prefixed listing', function (): void {
    blogArticle();

    $this->get('/en/blog')
        ->assertOk()
        ->assertSee('Launch of Bronber Auto Service')
        ->assertSee('#launch');
});

it('renders the article page with content blocks and related news', function (): void {
    blogArticle();
    blogArticle('partnerstvo-s-proizvoditelyami');

    $this->get('/blog/zapusk-bronber-auto-service')
        ->assertOk()
        ->assertSee('Тестовый лид статьи')
        ->assertSee('article-page-block__images')
        ->assertSee('Перейти к направлению')
        ->assertSee('Другие новости')
        ->assertSee('Запуск Bronber Auto Service');
});

it('shows 404 for an unknown article slug', function (): void {
    blogArticle();

    $this->get('/blog/nonexistent-slug')->assertNotFound();
});

it('hides draft articles from the listing and the article page', function (): void {
    blogArticle();

    Article::factory()->draft()
        ->has(ArticleTranslation::factory()->ru()->state([
            'title' => 'Черновая статья',
            'content' => articleBlocks('ru'),
        ]), 'translations')
        ->create(['slug' => 'draft-article', 'published_at' => '2025-12-31']);

    $this->get('/blog')->assertOk()->assertDontSee('Черновая статья');
    $this->get('/blog/draft-article')->assertNotFound();
});

it('falls back to the ru translation on the en route', function (): void {
    Article::factory()
        ->has(ArticleTranslation::factory()->ru()->state([
            'title' => 'Только русская статья',
            'content' => articleBlocks('ru'),
        ]), 'translations')
        ->create(['slug' => 'ru-only-article', 'published_at' => '2025-12-18']);

    $this->get('/en/blog/ru-only-article')
        ->assertOk()
        ->assertSee('Только русская статья')
        ->assertSee('Тестовый лид статьи');
});

it('renders the seo tags from the article translation', function (): void {
    blogArticle();

    $html = $this->get('/blog/zapusk-bronber-auto-service')->getContent();

    expect($html)
        ->toContain('<title>Запуск — Bronber</title>')
        ->toContain('<meta name="description" content="Описание статьи для SEO">')
        ->toContain('<link rel="canonical" href="'.url('/blog/zapusk-bronber-auto-service').'">')
        ->toContain('<link rel="alternate" hreflang="ru" href="'.url('/blog/zapusk-bronber-auto-service').'">')
        ->toContain('<link rel="alternate" hreflang="en" href="'.url('/en/blog/zapusk-bronber-auto-service').'">');
});
