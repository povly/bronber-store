<?php

use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Services\Languages\LanguageService;
use Database\Seeders\ContactsPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);
});

/**
 * contacts-main blocks for the seeded page.
 *
 * @return list<array<string, mixed>>
 */
function contactsBlocks(string $title, string $subtitle = 'Оставьте свою заявку'): array
{
    return [[
        '_type' => 'contacts-main',
        'title' => $title,
        'subtitle' => $subtitle,
        'phone' => '+7 (985) 449-8000',
        'email' => 'office@bronber.test',
        'address' => 'Москва, Пресненская набережная 12',
        'map_html' => '<iframe src="https://yandex.ru/map-widget/v1/?ll=37.539822%2C55.748792&z=15" width="100%" height="100%" frameborder="0" loading="lazy"></iframe>',
        'submit_label' => '',
        'consent_text' => '',
    ]];
}

function contactsPage(): Page
{
    return Page::factory()
        ->has(PageTranslation::factory()->ru()->state([
            'title' => 'Контакты',
            'meta_title' => 'Контакты — Bronber',
            'meta_description' => 'Тестовое описание страницы контактов',
            'content' => contactsBlocks('Свяжитесь <br>с нами'),
        ]), 'translations')
        ->has(PageTranslation::factory()->en()->state([
            'title' => 'Contacts',
            'content' => contactsBlocks('Get in <br>touch', 'Leave a request'),
        ]), 'translations')
        ->create(['slug' => 'contacts']);
}

it('renders the contacts page from db blocks', function (): void {
    contactsPage();

    $response = $this->get('/contacts')->assertOk();

    $response->assertSee('Свяжитесь')
        ->assertSee('+7 (985) 449-8000')
        ->assertSee('Москва, Пресненская набережная 12');

    $html = $response->getContent();

    expect($html)
        ->toContain('yandex.ru/map-widget/v1/')
        ->toContain('method="POST"')
        ->toContain('name="name"')
        ->toContain('name="phone"')
        ->toContain('name="email"')
        ->toContain('name="message"');
});

it('shows 404 when the contacts page is missing', function (): void {
    $this->get('/contacts')->assertNotFound();
});

it('shows 404 when the contacts page is a draft', function (): void {
    Page::factory()->draft()
        ->has(PageTranslation::factory()->ru()->state([
            'content' => contactsBlocks('Черновые контакты'),
        ]), 'translations')
        ->create(['slug' => 'contacts']);

    $this->get('/contacts')->assertNotFound();
});

it('renders the en translation on the locale-prefixed route', function (): void {
    contactsPage();

    $this->get('/en/contacts')
        ->assertOk()
        ->assertSee('Get in')
        ->assertSee('Leave a request');
});

it('renders breadcrumbs with the current page title', function (): void {
    contactsPage();

    $html = $this->get('/contacts')->getContent();

    expect($html)
        ->toContain('breadcrumbs')
        ->toContain('Главная')
        ->toContain('BreadcrumbList');
});

it('seeds demo content into an empty translation only', function (): void {
    $page = Page::factory()->create(['slug' => 'contacts']);
    PageTranslation::factory()->ru()->create(['page_id' => $page->getKey(), 'content' => null]);

    $this->seed(ContactsPageSeeder::class);

    $ru = $page->translations()->firstWhere('locale', 'ru');
    expect($ru->content)->not->toBeEmpty();

    $this->get('/contacts')->assertOk()->assertSee('Открыты');

    // Authored content is never overwritten by the seeder.
    $ru->update(['content' => contactsBlocks('Авторский заголовок')]);
    $this->seed(ContactsPageSeeder::class);

    expect($page->translations()->firstWhere('locale', 'ru')->content[0]['title'])->toBe('Авторский заголовок');
});
