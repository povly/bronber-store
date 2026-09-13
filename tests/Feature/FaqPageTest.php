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

function faqPage(): Page
{
    return Page::factory()
        ->has(PageTranslation::factory()->ru()->state([
            'title' => 'Часто задаваемые вопросы',
            'meta_title' => 'FAQ — Bronber',
            'meta_description' => 'Ответы на частые вопросы магазина',
            'content' => [[
                '_type' => 'faq-items',
                'title' => 'Часто задаваемые вопросы',
                'items' => [
                    ['_type' => 'item', 'question' => 'Тестовый вопрос из блоков?', 'answer' => "Тестовый ответ:\n\nПервая строка."],
                    ['_type' => 'item', 'question' => 'Второй тестовый вопрос?', 'answer' => 'Второй тестовый ответ.'],
                ],
            ]],
        ]), 'translations')
        ->has(PageTranslation::factory()->en()->state([
            'title' => 'Frequently Asked Questions',
            'meta_title' => 'FAQ — Bronber EN',
            'content' => [[
                '_type' => 'faq-items',
                'title' => 'Frequently Asked Questions',
                'items' => [
                    ['_type' => 'item', 'question' => 'Test question from blocks?', 'answer' => 'Test answer.'],
                ],
            ]],
        ]), 'translations')
        ->create(['slug' => 'faq']);
}

it('renders the faq page from db blocks', function (): void {
    faqPage();

    $this->get('/faq')
        ->assertOk()
        ->assertSee('Часто задаваемые вопросы')
        ->assertSee('Тестовый вопрос из блоков?')
        ->assertSee('Второй тестовый ответ.');
});

it('falls back to the static prototype when the faq page is missing', function (): void {
    $this->get('/faq')
        ->assertOk()
        ->assertSee('Как оформить заказ?')
        ->assertSee('faq()');
});

it('falls back to the static prototype when the faq page is a draft', function (): void {
    Page::factory()->draft()
        ->has(PageTranslation::factory()->ru()->state([
            'content' => [[
                '_type' => 'faq-items',
                'items' => [
                    ['_type' => 'item', 'question' => 'Черновой вопрос?', 'answer' => 'Не должен показываться.'],
                ],
            ]],
        ]), 'translations')
        ->create(['slug' => 'faq']);

    $this->get('/faq')
        ->assertOk()
        ->assertSee('Как оформить заказ?')
        ->assertDontSee('Черновой вопрос?');
});

it('renders the en translation on the locale-prefixed route', function (): void {
    faqPage();

    $this->get('/en/faq')
        ->assertOk()
        ->assertSee('Frequently Asked Questions')
        ->assertSee('Test question from blocks?');
});

it('renders the seo tags from the faq translation', function (): void {
    faqPage();

    $html = $this->get('/faq')->getContent();

    expect($html)
        ->toContain('<title>FAQ — Bronber</title>')
        ->toContain('<meta name="description" content="Ответы на частые вопросы магазина">')
        ->toContain('<link rel="canonical" href="'.url('/faq').'">')
        ->toContain('<link rel="alternate" hreflang="ru" href="'.url('/faq').'">')
        ->toContain('<link rel="alternate" hreflang="en" href="'.url('/en/faq').'">');
});
