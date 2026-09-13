<?php

use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Services\Languages\LanguageService;
use App\Support\PageBlocks\PageOptions;
use App\Support\PageBreadcrumbs;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();
    PageOptions::flush();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);

    app()->setLocale('ru');
});

function breadcrumbPage(string $slug, string $title, ?Page $parent = null): Page
{
    $page = Page::factory()
        ->when($parent !== null, fn ($factory) => $factory->childOf($parent))
        ->create(['slug' => $slug]);

    PageTranslation::factory()->ru()->for($page, 'page')->create(['title' => $title]);

    return $page;
}

it('builds the home → current trail for a root page', function (): void {
    $page = breadcrumbPage('faq', 'Часто задаваемые вопросы');

    expect(PageBreadcrumbs::forPage($page))->toBe([
        ['label' => 'Главная', 'url' => route('home')],
        ['label' => 'Часто задаваемые вопросы', 'url' => null],
    ]);
});

it('walks ancestors root-first for a nested page', function (): void {
    $parent = breadcrumbPage('delivery', 'Доставка');
    $child = breadcrumbPage('delivery-moscow', 'Доставка по Москве', $parent);

    expect(PageBreadcrumbs::forPage($child))->toBe([
        ['label' => 'Главная', 'url' => route('home')],
        ['label' => 'Доставка', 'url' => url('/delivery')],
        ['label' => 'Доставка по Москве', 'url' => null],
    ]);
});

it('localizes ancestor and home labels for a non-default locale', function (): void {
    $parent = breadcrumbPage('delivery', 'Доставка');
    $child = breadcrumbPage('delivery-moscow', 'Доставка по Москве', $parent);

    PageTranslation::factory()->en()->for($child, 'page')->create(['title' => 'Moscow delivery']);

    app()->setLocale('en');

    expect(PageBreadcrumbs::forPage($child))->toBe([
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Доставка', 'url' => url('/en/delivery')],
        ['label' => 'Moscow delivery', 'url' => null],
    ]);
});

it('skips unpublished ancestors', function (): void {
    $draft = Page::factory()->draft()->create(['slug' => 'draft-parent']);
    PageTranslation::factory()->ru()->for($draft, 'page')->create(['title' => 'Черновик']);
    $child = breadcrumbPage('child', 'Ребёнок', $draft);

    expect(PageBreadcrumbs::forPage($child))->toBe([
        ['label' => 'Главная', 'url' => route('home')],
        ['label' => 'Ребёнок', 'url' => null],
    ]);
});

it('cuts a cyclic parent chain instead of looping', function (): void {
    $first = Page::factory()->create(['slug' => 'a']);
    $second = Page::factory()->childOf($first)->create(['slug' => 'b']);
    $first->forceFill(['parent_id' => $second->getKey()])->save();

    PageTranslation::factory()->ru()->for($first, 'page')->create(['title' => 'A']);
    PageTranslation::factory()->ru()->for($second, 'page')->create(['title' => 'B']);

    $trail = PageBreadcrumbs::forPage($second);

    expect($trail[0])->toBe(['label' => 'Главная', 'url' => route('home')])
        ->and($trail[count($trail) - 1])->toBe(['label' => 'B', 'url' => null]);
});

it('renders page-level breadcrumbs and hides them on the site root', function (): void {
    breadcrumbPage('o-kompanii', 'О компании');

    $html = $this->get('/o-kompanii')->getContent();

    expect($html)
        ->toContain('breadcrumbs__list')
        ->toContain('href="'.url('/').'"')
        ->toContain('О компании');

    $home = Page::factory()->create(['slug' => 'index']);
    PageTranslation::factory()->ru()->for($home, 'page')->create(['title' => 'Главная страница']);

    expect($this->get('/')->getContent())->not->toContain('breadcrumbs__list');
});
