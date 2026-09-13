<?php

use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Services\Languages\LanguageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);
});

afterEach(function (): void {
    File::deleteDirectory(storage_path('app/public/about-test'));
});

/**
 * EditorJs payload stored by the admin editor (same shape as AboutPageSeeder).
 */
function aboutEditorJsText(string ...$paragraphs): string
{
    return (string) json_encode([
        'time' => 0,
        'blocks' => array_map(
            static fn (string $text): array => ['type' => 'paragraph', 'data' => ['text' => $text]],
            $paragraphs,
        ),
        'version' => '1.0.0',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function aboutPage(): Page
{
    return Page::factory()
        ->has(PageTranslation::factory()->ru()->state([
            'title' => 'О компании',
            'meta_title' => 'О компании — Bronber',
            'meta_description' => 'История компании Bronber',
            'content' => [[
                '_type' => 'about-timeline',
                'title' => 'История компании',
                'items' => [
                    ['_type' => 'item', 'date' => '2017', 'text' => aboutEditorJsText('Первый магазин открыт.', 'Топливные насосы напрямую от дистрибьюторов.')],
                    ['_type' => 'item', 'date' => '2021', 'text' => aboutEditorJsText('Более 10 000 клиентов купили запчасти.')],
                ],
            ]],
        ]), 'translations')
        ->has(PageTranslation::factory()->en()->state([
            'title' => 'About Us',
            'meta_title' => 'About Bronber EN',
            'content' => [[
                '_type' => 'about-timeline',
                'title' => 'Our Story',
                'items' => [
                    ['_type' => 'item', 'date' => '2017', 'text' => aboutEditorJsText('First store opened.')],
                ],
            ]],
        ]), 'translations')
        ->create(['slug' => 'about']);
}

it('renders the about page from db blocks', function (): void {
    aboutPage();

    $this->get('/about')
        ->assertOk()
        ->assertSee('История компании')
        ->assertSee('2017')
        ->assertSee('Первый магазин открыт.')
        ->assertSee('Более 10 000 клиентов купили запчасти.');
});

it('shows 404 when the about page is missing', function (): void {
    $this->get('/about')->assertNotFound();
});

it('shows 404 when the about page is a draft', function (): void {
    Page::factory()->draft()
        ->has(PageTranslation::factory()->ru()->state([
            'content' => [[
                '_type' => 'about-timeline',
                'title' => 'История компании',
                'items' => [
                    ['_type' => 'item', 'date' => '2017', 'text' => aboutEditorJsText('Черновой текст не должен показываться.')],
                ],
            ]],
        ]), 'translations')
        ->create(['slug' => 'about']);

    $this->get('/about')->assertNotFound();
});

it('renders the en translation on the locale-prefixed route', function (): void {
    aboutPage();

    $this->get('/en/about')
        ->assertOk()
        ->assertSee('Our Story')
        ->assertSee('First store opened.');
});

it('renders the seo tags from the about translation', function (): void {
    aboutPage();

    $html = $this->get('/about')->getContent();

    expect($html)
        ->toContain('<title>О компании — Bronber</title>')
        ->toContain('<meta name="description" content="История компании Bronber">')
        ->toContain('<link rel="canonical" href="'.url('/about').'">')
        ->toContain('<link rel="alternate" hreflang="ru" href="'.url('/about').'">')
        ->toContain('<link rel="alternate" hreflang="en" href="'.url('/en/about').'">');
});

it('renders editorjs mediaImage images through x-img with converted formats', function (): void {
    File::ensureDirectoryExists(storage_path('app/public/about-test'));
    File::put(storage_path('app/public/about-test/phone.jpg'), 'jpg-bytes');
    File::put(storage_path('app/public/about-test/phone.webp'), 'webp-bytes');

    Page::factory()
        ->has(PageTranslation::factory()->ru()->state([
            'title' => 'О компании',
            'meta_title' => 'О компании — Bronber',
            'meta_description' => 'История компании Bronber',
            'content' => [[
                '_type' => 'about-timeline',
                'title' => 'История компании',
                'items' => [
                    ['_type' => 'item', 'date' => '2017', 'text' => (string) json_encode([
                        'time' => 0,
                        'blocks' => [
                            ['type' => 'paragraph', 'data' => ['text' => 'Событие с изображением.']],
                            ['type' => 'mediaImage', 'data' => [
                                'files' => [
                                    ['path' => 'about-test/phone.jpg', 'url' => url('/storage/about-test/phone.jpg')],
                                    ['path' => 'about-test/desktop.jpg', 'url' => url('/storage/about-test/desktop.jpg')],
                                ],
                                'caption' => '',
                            ]],
                        ],
                        'version' => '1.0.0',
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
                ],
            ]],
        ]), 'translations')
        ->create(['slug' => 'about']);

    $html = $this->get('/about')->getContent();

    expect($html)
        ->toContain('about__image-wrap')
        ->toContain('about__image')
        ->toContain(url('/storage/about-test/phone.webp'))
        ->not->toContain('data-src="'.url('/storage/about-test/phone.jpg'));
});

it('renders separate desktop and mobile headings for timeline items', function (): void {
    Page::factory()
        ->has(PageTranslation::factory()->ru()->state([
            'title' => 'О компании',
            'meta_title' => 'О компании — Bronber',
            'meta_description' => 'История компании Bronber',
            'content' => [[
                '_type' => 'about-timeline',
                'title' => 'История компании',
                'items' => [
                    [
                        '_type' => 'item',
                        'date' => '2017',
                        'title_desktop' => 'Год основания',
                        'title_mobile' => 'Как всё началось',
                        'text' => aboutEditorJsText('Первый магазин открыт.'),
                    ],
                    [
                        '_type' => 'item',
                        'date' => '2018',
                        'text' => aboutEditorJsText('Расширили склад.'),
                    ],
                ],
            ]],
        ]), 'translations')
        ->create(['slug' => 'about']);

    $html = $this->get('/about')->getContent();

    expect($html)
        ->toContain('Год основания')
        ->toContain('Как всё началось')
        ->toContain('2018')
        ->not->toContain('2017');
});

it('renders mediaImage with the stock package markup outside the about page', function (): void {
    $body = (string) json_encode([
        'time' => 0,
        'blocks' => [
            ['type' => 'paragraph', 'data' => ['text' => 'Текст legacy блока.']],
            ['type' => 'mediaImage', 'data' => [
                'files' => [
                    ['path' => 'about-test/phone.jpg', 'url' => url('/storage/about-test/phone.jpg')],
                    ['path' => 'about-test/desktop.jpg', 'url' => url('/storage/about-test/desktop.jpg')],
                ],
                'caption' => '',
            ]],
        ],
        'version' => '1.0.0',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    Page::factory()
        ->has(PageTranslation::factory()->ru()->state([
            'title' => 'Медиа-тест',
            'content' => [['_type' => 'text', 'body' => $body]],
        ]), 'translations')
        ->create(['slug' => 'media-test']);

    $html = $this->get('/media-test')->getContent();

    expect($html)
        ->toContain('<img src="'.url('/storage/about-test/phone.jpg').'"')
        ->toContain('<img src="'.url('/storage/about-test/desktop.jpg').'"')
        ->not->toContain('about__image-wrap')
        ->not->toContain('mm-editorjs-gallery__item')
        ->not->toContain('data-src="'.url('/storage/about-test/'));
});
