<?php

namespace App\Providers;

use App\Support\PageBlocks\SettingsResolver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as IlluminateView;
use MoonShine\Laravel\Http\Middleware\Authenticate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[\Override]
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::usePreloadTagAttributes(fn (): false => false);

        $this->app['view']->addLocation(resource_path('views/blocks'));

        // Runs after every provider (including discovered packages) has
        // booted, so late-registered moonshine routes are covered too.
        $this->app->booted(function (): void {
            $this->requireMoonshineAuth();
        });

        View::share('favorites', json_decode($_COOKIE['favorites'] ?? '[]', true) ?? []);

        View::composer(
            ['blocks.common.header.header', 'blocks.common.footer.footer', 'blocks.common.mobile-menu.mobile-menu', 'blocks.common.mobile-nav.mobile-nav'],
            static function (IlluminateView $illuminateView): void {
                $name = $illuminateView->getName();

                [$variable, $settings] = match (true) {
                    str_contains($name, 'header') => ['headerSettings', SettingsResolver::header()],
                    str_contains($name, 'footer') => ['footerSettings', SettingsResolver::footer()],
                    str_contains($name, 'mobile-menu') => ['mobileMenuSettings', SettingsResolver::mobileMenu()],
                    default => ['mobileNavSettings', SettingsResolver::mobileNav()],
                };

                $hasSettings = in_array(true, array_map(
                    static fn ($value): bool => ! empty($value),
                    $settings,
                ), true);

                $illuminateView->with($variable, $settings);

                Log::debug('[Layout] settings resolved, context={context} source={source}', [
                    'context' => str_replace('Settings', '', $variable),
                    'source' => $hasSettings ? 'settings' : 'fallback',
                ]);
            },
        );

        View::composer(['blocks.common.header.header', 'layouts.app'], function (IlluminateView $illuminateView): void {
            $searchTypes = collect(config('search.types'))->map(fn (array $type): array => [
                'value' => $type['value'],
                'label' => __($type['label']),
            ])->all();

            $illuminateView->with('searchTypes', $searchTypes);
            $illuminateView->with('availableLocales', config('app.available_locales'));
        });

        View::composer('*', fn (IlluminateView $illuminateView) => $illuminateView->with('catalogCategories', $this->catalogCategories()));
    }

    /**
     * Attach MoonShine auth middleware to every moonshine route except the
     * auth routes themselves.
     *
     * MoonShine 4.15's Route::moonshine() macro silently drops
     * `withAuthenticate: true` when the default route group defines
     * middleware (it always does: `middleware => 'moonshine'`), so package
     * routes (media manager, image editor, editorjs field, …) registered
     * with that flag stay reachable by guests. Core controller routes are
     * protected separately inside DefaultRoutes; adding the middleware
     * again there is harmless.
     *
     * Note: requires non-cached routes (no `route:cache`), which holds for
     * this project's current deployment.
     */
    private function requireMoonshineAuth(): void
    {
        $authRoutes = ['moonshine.login', 'moonshine.authenticate', 'moonshine.logout'];

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();

            if ($name === null || ! str_starts_with($name, 'moonshine.') || in_array($name, $authRoutes, true)) {
                continue;
            }

            $route->middleware(Authenticate::class);
        }
    }

    /**
     * Static catalog categories tree (pre-MVP hardcoded data).
     *
     * Shared with all views via composer. Reused by header catalog-menu
     * and (later) by the home categories grid.
     *
     * @return array<int, array{name: string, slug: string, href: string, children: array<int, array{name: string, href: string}>}>
     */
    private function catalogCategories(): array
    {
        $children = [
            'brake-system' => [
                ['name' => 'Комплект тормозной системы', 'href' => '#'],
                ['name' => 'Комплекты карбон-керамической тормозной системы', 'href' => '#'],
                ['name' => 'Тормозные суппорты', 'href' => '#'],
                ['name' => 'Армированные тормозные шланги', 'href' => '#'],
                ['name' => 'Крепления и адаптеры суппортов', 'href' => '#'],
            ],
            'chip-tuning' => [
                ['name' => 'Прошивки Stage 1', 'href' => '#'],
                ['name' => 'Прошивки Stage 2', 'href' => '#'],
                ['name' => 'Прошивки Stage 3', 'href' => '#'],
                ['name' => 'Оборудование для прошивки', 'href' => '#'],
            ],
            'wheels' => [
                ['name' => 'Литые диски', 'href' => '#'],
                ['name' => 'Кованые диски', 'href' => '#'],
                ['name' => 'Шины', 'href' => '#'],
                ['name' => 'Болты и гайки', 'href' => '#'],
            ],
            'optics' => [
                ['name' => 'Передние фары', 'href' => '#'],
                ['name' => 'Задние фонари', 'href' => '#'],
                ['name' => 'Противотуманные фары', 'href' => '#'],
                ['name' => 'Дневные ходовые огни', 'href' => '#'],
            ],
            'intake' => [
                ['name' => 'Воздушные фильтры', 'href' => '#'],
                ['name' => 'Впускные коллекторы', 'href' => '#'],
                ['name' => 'Дроссельные заслонки', 'href' => '#'],
                ['name' => 'Турбокомпрессоры', 'href' => '#'],
            ],
            'suspension' => [
                ['name' => 'Амортизаторы', 'href' => '#'],
                ['name' => 'Пружины подвески', 'href' => '#'],
                ['name' => 'Рычаги подвески', 'href' => '#'],
                ['name' => 'Стабилизаторы', 'href' => '#'],
            ],
            'downpipes' => [
                ['name' => 'Даунпайпы нержавеющая сталь', 'href' => '#'],
                ['name' => 'Даунпайпы титан', 'href' => '#'],
                ['name' => 'Приёмные трубы', 'href' => '#'],
                ['name' => 'Катализаторы', 'href' => '#'],
            ],
            'exhaust' => [
                ['name' => 'Глушители', 'href' => '#'],
                ['name' => 'Резонаторы', 'href' => '#'],
                ['name' => 'Насадки на выхлоп', 'href' => '#'],
                ['name' => 'Полные выхлопные системы', 'href' => '#'],
            ],
            'carbon' => [
                ['name' => 'Карбоновый обвес кузова', 'href' => '#'],
                ['name' => 'Карбоновое антикрыло', 'href' => '#'],
                ['name' => 'Карбоновые зеркала', 'href' => '#'],
                ['name' => 'Карбоновая решётка радиатора', 'href' => '#'],
            ],
            'oils' => [
                ['name' => 'Моторные масла', 'href' => '#'],
                ['name' => 'Трансмиссионные масла', 'href' => '#'],
                ['name' => 'Тормозные жидкости', 'href' => '#'],
                ['name' => 'Антифризы', 'href' => '#'],
            ],
        ];

        $slugs = [
            ['name' => 'Тормозная система', 'slug' => 'brake-system'],
            ['name' => 'Чип тюнинг', 'slug' => 'chip-tuning'],
            ['name' => 'Диски', 'slug' => 'wheels'],
            ['name' => 'Оптика', 'slug' => 'optics'],
            ['name' => 'Впускная система', 'slug' => 'intake'],
            ['name' => 'Подвеска', 'slug' => 'suspension'],
            ['name' => 'Приемные трубы и даунпайпы', 'slug' => 'downpipes'],
            ['name' => 'Выхлопные системы', 'slug' => 'exhaust'],
            ['name' => 'Карбоновые элементы', 'slug' => 'carbon'],
            ['name' => 'Масла и жидкости', 'slug' => 'oils'],
        ];

        return collect($slugs)->map(fn (array $cat): array => [
            'name' => $cat['name'],
            'slug' => $cat['slug'],
            'href' => route('catalog', ['category' => $cat['slug']]),
            'image' => '/images/catalog/'.$cat['slug'],
            'children' => $children[$cat['slug']] ?? [],
        ])->all();
    }
}
