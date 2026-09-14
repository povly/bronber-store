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
 * Loyalty blocks in the shape stored by the admin flexible layouts
 * (same shape as LoyaltyPageSeeder): hero with a custom-url button,
 * benefits with one titled «main» item, how-works steps and the
 * bottom example/gift/faq columns. Benefits/how-works texts are
 * EditorJS documents — the block views render them via RenderEditorJs.
 *
 * @return list<array<string, mixed>>
 */
function loyaltyContent(string $locale): array
{
    $ru = $locale === 'ru';

    $editorjs = static fn (string $html): string => (string) json_encode(
        ['time' => 0, 'blocks' => [['type' => 'paragraph', 'data' => ['text' => $html]]], 'version' => '1.0.0'],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
    );

    return [
        [
            '_type' => 'loyalty-hero',
            'title' => $ru ? 'Программа лояльности' : 'Loyalty Program',
            'text' => $ru ? 'Покупайте запчасти и получайте бонусы' : 'Buy parts and earn bonuses',
            'label' => $ru ? 'Зарегистрироваться' : 'Sign Up',
            'type' => 'custom',
            'page' => null,
            'url' => '/register-me',
            // image_pc is set for ru only — en leaves it empty on purpose
            // so the MediaFallback inheritance is exercised for real.
            'image_pc' => $ru ? '/images/loyalty/1.png' : null,
            'image_mb' => null,
        ],
        [
            '_type' => 'loyalty-benefits',
            'items' => [
                ['_type' => 'item', 'icon' => '/images/loyalty/icons/benefits-coin.svg', 'title' => '1 бонус = 1 ₽', 'text' => $editorjs('Оплатите до 30% заказа')],
                ['_type' => 'item', 'icon' => '/images/loyalty/icons/benefits-cart.svg', 'title' => null, 'text' => $editorjs('Бонусы с каждой покупки')],
            ],
        ],
        [
            '_type' => 'loyalty-how-works',
            'title' => $ru ? 'Как это работает?' : 'How does it work?',
            'items' => [
                ['_type' => 'item', 'icon' => '/images/loyalty/icons/how-cart.svg', 'title' => 'Шаг 1', 'text' => $editorjs('Описание шага')],
            ],
        ],
        [
            '_type' => 'loyalty-bottom',
            'title' => $ru ? 'Пример начисления бонусов' : 'Bonus accrual example',
            'rows' => [
                ['_type' => 'row', 'left_text' => $ru ? 'Сумма заказа' : 'Order amount', 'right_text' => '10,000 ₽', 'color' => null],
                ['_type' => 'row', 'left_text' => $ru ? 'Начислено бонусов' : 'Bonuses accrued', 'right_text' => '100 ₽', 'color' => '#7212BC'],
            ],
            'gift_icon' => '/images/loyalty/icons/bottom-gift.svg',
            'gift_title' => $ru ? 'Чем больше покупок' : 'More purchases',
            'gift_text' => 'Bronber',
            'faq_title' => $ru ? 'Частые вопросы' : 'FAQ',
            'faqs' => [
                ['_type' => 'item', 'question' => $ru ? 'Когда бонусы доступны?' : 'When available?', 'answer' => $ru ? 'После оплаты заказа.' : 'After payment.'],
            ],
        ],
    ];
}

function loyaltyPage(): Page
{
    return Page::factory()
        ->has(PageTranslation::factory()->ru()->state([
            'title' => 'Программа лояльности',
            'meta_title' => 'Программа лояльности — Bronber',
            'content' => loyaltyContent('ru'),
        ]), 'translations')
        ->has(PageTranslation::factory()->en()->state([
            'title' => 'Loyalty Program',
            'meta_title' => 'Loyalty Program — Bronber EN',
            'content' => loyaltyContent('en'),
        ]), 'translations')
        ->create(['slug' => 'loyalty']);
}

it('renders the loyalty page from db flexible blocks', function (): void {
    loyaltyPage();

    $response = $this->get('/loyalty');

    $response->assertOk()
        ->assertSee('Программа лояльности')
        ->assertSee('Покупайте запчасти и получайте бонусы')
        ->assertSee('Как это работает?')
        ->assertSee('Пример начисления бонусов')
        ->assertSee('Чем больше покупок')
        ->assertSee('Когда бонусы доступны?')
        ->assertSee('После оплаты заказа.');
});

it('renders the hero button with the resolved custom link', function (): void {
    loyaltyPage();

    $html = $this->get('/loyalty')->getContent();

    expect($html)
        ->toContain('<a href="/register-me" class="loyalty-hero__btn btn btn--primary">')
        ->toContain('Зарегистрироваться')
        ->toContain('loyalty-hero__image--pc');
});

it('marks a titled benefit item as main and renders others without a title', function (): void {
    loyaltyPage();

    $html = $this->get('/loyalty')->getContent();

    expect($html)
        ->toContain('loyalty-benefits__item--main')
        ->toContain('loyalty-benefits__title">1 бонус = 1 ₽');

    $secondItem = substr($html, (int) strpos($html, 'Бонусы с каждой покупки') - 300, 300);
    expect($secondItem)->not->toContain('loyalty-benefits__item--main');
});

it('colors the accent row inline instead of the prototype modifier class', function (): void {
    loyaltyPage();

    $html = $this->get('/loyalty')->getContent();

    expect($html)
        ->toContain('style="color: #7212BC"')
        ->not->toContain('loyalty-bottom__example-row--accent');
});

it('renders the en translation on the locale-prefixed route', function (): void {
    loyaltyPage();

    $this->get('/en/loyalty')
        ->assertOk()
        ->assertSee('Loyalty Program')
        ->assertSee('How does it work?')
        ->assertSee('When available?');
});

it('inherits hero media from the default locale when en leaves it empty', function (): void {
    loyaltyPage();

    // image_pc/image_mb are null in the en translation — MediaFallback
    // copies the ru values in at render time.
    $html = $this->get('/en/loyalty')->getContent();

    expect($html)->toContain('images/loyalty/1');
});

it('omits the hero button when the link is broken', function (): void {
    Page::factory()
        ->has(PageTranslation::factory()->ru()->state([
            'title' => 'Программа лояльности',
            'content' => [['_type' => 'loyalty-hero', 'title' => 'Программа лояльности', 'label' => 'Кнопка', 'type' => 'custom', 'url' => '']],
        ]), 'translations')
        ->create(['slug' => 'loyalty']);

    $html = $this->get('/loyalty')->getContent();

    expect($html)
        ->toContain('loyalty-hero__title')
        ->not->toContain('loyalty-hero__btn');
});

it('shows 404 when the loyalty page is missing', function (): void {
    $this->get('/loyalty')->assertNotFound();
});

it('shows 404 when the loyalty page is a draft', function (): void {
    Page::factory()->draft()
        ->has(PageTranslation::factory()->ru()->state([
            'content' => loyaltyContent('ru'),
        ]), 'translations')
        ->create(['slug' => 'loyalty']);

    $this->get('/loyalty')->assertNotFound();
});
