<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Database\Seeder;

/**
 * Demo pages to exercise the DB-page pipeline (rendering, translations, SEO).
 * Slug intentionally does not collide with fixed prototype routes.
 */
class DemoPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::query()->firstOrCreate(
            ['slug' => 'o-kompanii'],
            ['is_published' => true, 'sort_order' => 0],
        );

        PageTranslation::query()->firstOrCreate(
            ['page_id' => $page->getKey(), 'locale' => 'ru'],
            [
                'title' => 'О компании Bronber',
                'meta_title' => 'О компании Bronber — топливные насосы для BMW, Audi, VW',
                'meta_description' => 'Bronber — магазин автозапчастей: топливные насосы и компоненты для немецких автомобилей. Оригиналы и сертифицированные аналоги.',
                'meta_keywords' => 'топливные насосы, автозапчасти, bmw, audi, vw',
                'content' => [
                    ['_type' => 'hero', 'title' => 'О компании Bronber', 'subtitle' => 'Топливные насосы и компоненты для BMW, Audi и Volkswagen с 2017 года'],
                    ['_type' => 'text', 'body' => '{"time":0,"blocks":[{"type":"paragraph","data":{"text":"Мы поставляем оригинальные и сертифицированные топливные насосы для немецких автомобилей напрямую от официальных дистрибьюторов."}}],"version":"1.0.0"}'],
                    ['_type' => 'featured-products', 'title' => 'Популярные товары', 'count' => 4],
                ],
            ],
        );

        PageTranslation::query()->firstOrCreate(
            ['page_id' => $page->getKey(), 'locale' => 'en'],
            [
                'title' => 'About Bronber',
                'meta_title' => 'About Bronber — fuel pumps for BMW, Audi, VW',
                'meta_description' => 'Bronber is an auto parts store: fuel pumps and components for German cars. Originals and certified analogues.',
                'meta_keywords' => 'fuel pumps, auto parts, bmw, audi, vw',
                'content' => [
                    ['_type' => 'hero', 'title' => 'About Bronber', 'subtitle' => 'Fuel pumps and components for BMW, Audi and Volkswagen since 2017'],
                    ['_type' => 'featured-products', 'title' => 'Popular products', 'count' => 4],
                ],
            ],
        );
    }
}
