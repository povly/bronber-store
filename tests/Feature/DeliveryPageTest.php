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

function deliveryPage(): Page
{
    return Page::factory()
        ->has(PageTranslation::factory()->ru()->state([
            'title' => 'Доставка и оплата',
            'meta_title' => 'Доставка и оплата — Bronber',
            'meta_description' => 'Способы оплаты заказов автозапчастей',
            'content' => [
                [
                    '_type' => 'delivery-methods',
                    'title' => 'Для наших клиентов доступны следующие варианты оплаты заказа:',
                    'items' => [
                        ['_type' => 'item', 'title' => 'Оплата наличными', 'icon' => '/images/delivery/cash.svg', 'text' => 'Наличными при самовывозе в нашем магазине'],
                        ['_type' => 'item', 'title' => 'Оплата по СБП', 'icon' => '/images/delivery/sbp.svg', 'text' => 'QR-код для оплаты через СБП'],
                    ],
                ],
                [
                    '_type' => 'contact-list',
                    'title' => 'По вопросам доставки и оплаты:',
                    'items' => [
                        ['_type' => 'item', 'text' => '+7 (985) 449-8000', 'href' => 'tel:+79854498000', 'icon' => '/images/delivery/phone.svg'],
                    ],
                ],
            ],
        ]), 'translations')
        ->has(PageTranslation::factory()->en()->state([
            'title' => 'Delivery & Payment',
            'meta_title' => 'Delivery & Payment — Bronber EN',
            'content' => [
                [
                    '_type' => 'delivery-methods',
                    'title' => 'The following payment options are available for our customers:',
                    'items' => [
                        ['_type' => 'item', 'title' => 'Cash payment', 'icon' => '/images/delivery/cash.svg', 'text' => 'Pay in cash when picking up your order'],
                    ],
                ],
            ],
        ]), 'translations')
        ->create(['slug' => 'delivery']);
}

it('renders the delivery page from db blocks', function (): void {
    deliveryPage();

    $this->get('/delivery')
        ->assertOk()
        ->assertSee('Для наших клиентов доступны следующие варианты оплаты заказа:')
        ->assertSee('Оплата наличными')
        ->assertSee('delivery__method')
        ->assertSee('По вопросам доставки и оплаты:')
        ->assertSee('href="tel:+79854498000"', false)
        ->assertSee('/images/delivery/cash.svg', false);
});

it('shows 404 when the delivery page is missing', function (): void {
    $this->get('/delivery')->assertNotFound();
});

it('shows 404 when the delivery page is a draft', function (): void {
    Page::factory()->draft()
        ->has(PageTranslation::factory()->ru()->state([
            'content' => [
                [
                    '_type' => 'delivery-methods',
                    'items' => [
                        ['_type' => 'item', 'title' => 'Черновой способ оплаты', 'text' => 'Не должен показываться.'],
                    ],
                ],
            ],
        ]), 'translations')
        ->create(['slug' => 'delivery']);

    $this->get('/delivery')->assertNotFound();
});

it('renders the en translation on the locale-prefixed route', function (): void {
    deliveryPage();

    $this->get('/en/delivery')
        ->assertOk()
        ->assertSee('The following payment options are available for our customers:')
        ->assertSee('Cash payment');
});

it('renders breadcrumbs on the delivery page', function (): void {
    deliveryPage();

    $this->get('/delivery')
        ->assertOk()
        ->assertSee('Главная');
});
