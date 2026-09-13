<?php

use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Services\Languages\LanguageService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);
});

/**
 * EditorJs payload stored by the admin editor (same shape as
 * ReturnsPageSeeder): paragraphs, an h2 header and an unordered list.
 */
function returnsEditorJs(string ...$paragraphs): string
{
    return (string) json_encode([
        'time' => 0,
        'blocks' => array_merge(
            array_map(
                static fn (string $text): array => ['type' => 'paragraph', 'data' => ['text' => $text]],
                $paragraphs,
            ),
            [
                ['type' => 'header', 'data' => ['text' => 'Перечень товаров, не подлежащих возврату и обмену', 'level' => 2]],
                ['type' => 'list', 'data' => ['style' => 'unordered', 'items' => ['топливные насосы,', 'катушки и свечи зажигания,']]],
            ],
        ),
        'version' => '1.0.0',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function returnsPage(): Page
{
    return Page::factory()
        ->has(PageTranslation::factory()->ru()->state([
            'title' => 'Возврат и обмен',
            'meta_title' => 'Возврат и обмен — Bronber',
            'meta_description' => 'Условия возврата и обмена автозапчастей Bronber',
            'content' => [[
                '_type' => 'returns-content',
                'title' => 'Возврат и обмен',
                'body' => returnsEditorJs('Возврат товара надлежащего качества в течении 14-ти календарных дней.', 'Товары со следами установки возврату не подлежат.'),
            ]],
        ]), 'translations')
        ->has(PageTranslation::factory()->en()->state([
            'title' => 'Returns and Exchanges',
            'meta_title' => 'Returns and Exchanges — Bronber EN',
            'content' => [[
                '_type' => 'returns-content',
                'title' => 'Returns and Exchanges',
                'body' => returnsEditorJs('You can return a product within 14 calendar days.'),
            ]],
        ]), 'translations')
        ->create(['slug' => 'returns']);
}

it('renders the returns page from a db editorjs block', function (): void {
    returnsPage();

    $this->get('/returns')
        ->assertOk()
        ->assertSee('Возврат и обмен')
        ->assertSee('Возврат товара надлежащего качества в течении 14-ти календарных дней.')
        ->assertSee('Товары со следами установки возврату не подлежат.')
        ->assertSee('Перечень товаров, не подлежащих возврату и обмену')
        ->assertSee('топливные насосы,');
});

it('renders the editorjs markup as semantic html', function (): void {
    returnsPage();

    $html = $this->get('/returns')->getContent();

    expect($html)
        ->toContain('<h1 class="section__title">Возврат и обмен</h1>')
        ->toContain('<h2')
        ->toContain('<ul>')
        ->toContain('<li>');
});

it('shows 404 when the returns page is missing', function (): void {
    $this->get('/returns')->assertNotFound();
});

it('shows 404 when the returns page is a draft', function (): void {
    Page::factory()->draft()
        ->has(PageTranslation::factory()->ru()->state([
            'content' => [[
                '_type' => 'returns-content',
                'title' => 'Возврат и обмен',
                'body' => returnsEditorJs('Черновой текст не должен показываться.'),
            ]],
        ]), 'translations')
        ->create(['slug' => 'returns']);

    $this->get('/returns')->assertNotFound();
});

it('renders the en translation on the locale-prefixed route', function (): void {
    returnsPage();

    $this->get('/en/returns')
        ->assertOk()
        ->assertSee('Returns and Exchanges')
        ->assertSee('You can return a product within 14 calendar days.');
});

it('renders the seo tags from the returns translation', function (): void {
    returnsPage();

    $html = $this->get('/returns')->getContent();

    expect($html)
        ->toContain('<title>Возврат и обмен — Bronber</title>')
        ->toContain('<meta name="description" content="Условия возврата и обмена автозапчастей Bronber">')
        ->toContain('<link rel="canonical" href="'.url('/returns').'">')
        ->toContain('<link rel="alternate" hreflang="ru" href="'.url('/returns').'">')
        ->toContain('<link rel="alternate" hreflang="en" href="'.url('/en/returns').'">');
});
