<?php

use App\Models\Language;
use App\Models\Setting;
use App\Services\Languages\LanguageService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);

    app()->setLocale('ru');
});

/**
 * error-404 setting block in the shape stored by SettingsSeeder:
 * title, html description and two typed link buttons.
 *
 * @return list<array<string, mixed>>
 */
function error404Blocks(bool $english = false): array
{
    return [
        [
            '_type' => 'error-404',
            'title' => $english ? 'Page not found' : 'Страница не найдена',
            'text' => $english
                ? 'The page does not exist.<br>Check the address.'
                : 'Страницы не существует.<br>Проверьте адрес.',
            'buttons' => [
                ['label' => $english ? 'Go home' : 'На главную', 'type' => 'custom', 'page' => null, 'url' => $english ? '/en/' : '/', 'variant' => 'primary'],
                ['label' => $english ? 'Go to catalog' : 'Перейти в каталог', 'type' => 'custom', 'page' => null, 'url' => $english ? '/en/catalog' : '/catalog', 'variant' => 'white-border'],
            ],
        ],
    ];
}

it('renders the static prototype when no settings exist', function (): void {
    $response = $this->get('/nonexistent-page');

    $response->assertNotFound();

    $html = $response->getContent();

    expect($html)
        ->toContain('error-404__code">404<')
        ->toContain('Страница не найдена')
        ->toContain('На главную')
        ->toContain('Перейти в каталог');
});

it('renders the 404 block from settings with both button variants', function (): void {
    Setting::factory()->error404()->ru()->withBlocks(error404Blocks())->create();

    $html = $this->get('/nonexistent-page')->getContent();

    expect($html)
        ->toContain('error-404__code">404<')
        ->toContain('Страница не найдена')
        ->toContain('Страницы не существует.<br>Проверьте адрес.')
        ->toContain('href="/" class="btn btn--primary error-404__btn"')
        ->toContain('href="/catalog" class="btn btn--white-border error-404__btn"')
        ->toContain('На главную')
        ->toContain('Перейти в каталог');
});

it('renders the en setting on the locale-prefixed missing route', function (): void {
    Setting::factory()->error404()->en()->withBlocks(error404Blocks(english: true))->create();

    $html = $this->get('/en/nonexistent-page')->getContent();

    expect($html)
        ->toContain('Page not found')
        ->toContain('href="/en/" class="btn btn--primary error-404__btn"')
        ->toContain('href="/en/catalog" class="btn btn--white-border error-404__btn"');
});

it('falls back to the default language setting when en is missing', function (): void {
    Setting::factory()->error404()->ru()->withBlocks(error404Blocks())->create();

    $html = $this->get('/en/nonexistent-page')->getContent();

    expect($html)
        ->toContain('Страница не найдена')
        ->toContain('На главную');
});

it('omits buttons with broken links but keeps the page 404', function (): void {
    Setting::factory()->error404()->ru()->withBlocks([[
        '_type' => 'error-404',
        'title' => 'Страница не найдена',
        'text' => 'Проверьте адрес.',
        'buttons' => [
            ['label' => 'Пустая ссылка', 'type' => 'custom', 'page' => null, 'url' => '', 'variant' => 'primary'],
        ],
    ]])->create();

    $response = $this->get('/nonexistent-page');

    $response->assertNotFound();

    expect($response->getContent())
        ->toContain('Страница не найдена')
        ->not->toContain('Пустая ссылка');
});

it('never serves the 404 url as a successful page', function (): void {
    Setting::factory()->error404()->ru()->withBlocks(error404Blocks())->create();

    $this->get('/404')->assertNotFound();
});
