<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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

        View::share('favorites', json_decode($_COOKIE['favorites'] ?? '[]', true) ?? []);

        View::composer(['blocks.common.header.header', 'layouts.app'], function (\Illuminate\View\View $view): void {
            $searchTypes = collect(config('search.types'))->map(fn (array $type): array => [
                'value' => $type['value'],
                'label' => __($type['label']),
            ])->all();

            $view->with('searchTypes', $searchTypes);
            $view->with('availableLocales', config('app.available_locales'));
        });

        View::composer('*', fn (\Illuminate\View\View $view) => $view->with('catalogCategories', $this->catalogCategories()));
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
