<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Mock catalog data source for dynamic page blocks.
 *
 * Copy of the storefront prototype product data (views/routes mock arrays).
 * In the future this will be replaced by the CRM-warehouse API (subdomain
 * application); dynamic blocks (e.g. featured-products) already consume this
 * interface, so swapping the source will not touch block configuration.
 */
class CatalogMock
{
    /**
     * @return list<array<string, mixed>> product-card shaped data
     */
    public static function products(): array
    {
        return [
            ['title' => 'DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч', 'article' => 'DW-651-1008', 'image' => '/images/home/products/1/1.png', 'rating' => 4, 'reviewsCount' => 122, 'price' => 1100, 'oldPrice' => null, 'inStock' => true, 'sale' => 'ТОП'],
            ['title' => 'DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч', 'article' => 'DW-651-1008', 'image' => '/images/home/products/1/2.png', 'rating' => 5, 'reviewsCount' => 8, 'price' => 1100, 'oldPrice' => 1300, 'inStock' => true, 'sale' => '-15%'],
            ['title' => 'Bosch 0 580 464 070 Топливный насос электрический 12В', 'article' => '0 580 464 070', 'image' => '/images/home/products/1/3.png', 'rating' => 0, 'reviewsCount' => 0, 'price' => 2450, 'oldPrice' => 2900, 'inStock' => true, 'sale' => null],
            ['title' => 'Bosch 0 580 464 070 Топливный насос электрический 12В', 'article' => '0 580 464 070', 'image' => '/images/home/products/1/4.png', 'rating' => 4, 'reviewsCount' => 12, 'price' => 2450, 'oldPrice' => null, 'inStock' => false, 'sale' => 'Распродажа'],
            ['title' => 'Топливный модуль BMW 5 (E39) / X5 (E53) 3.0d', 'article' => '7 710 293', 'image' => '/images/home/products/1/1.png', 'rating' => 5, 'reviewsCount' => 31, 'price' => 8900, 'oldPrice' => null, 'inStock' => true, 'sale' => null],
            ['title' => 'Топливный насос Audi A4 B6 / A6 C5 1.9 TDI', 'article' => '1J0919050DQ', 'image' => '/images/home/products/1/2.png', 'rating' => 4, 'reviewsCount' => 17, 'price' => 5600, 'oldPrice' => 6400, 'inStock' => true, 'sale' => '-12%'],
            ['title' => 'Топливный насос VW Passat B5 / Golf 4 1.6', 'article' => '1J0919051A', 'image' => '/images/home/products/1/3.png', 'rating' => 5, 'reviewsCount' => 44, 'price' => 4300, 'oldPrice' => null, 'inStock' => true, 'sale' => null],
            ['title' => 'Кит ремкомплекта топливного насоса BMW N54', 'article' => '16117198315', 'image' => '/images/home/products/1/4.png', 'rating' => 0, 'reviewsCount' => 2, 'price' => 1800, 'oldPrice' => null, 'inStock' => true, 'sale' => null],
        ];
    }

    /**
     * Featured products for the featured-products dynamic block.
     *
     * @param  int<1, max>  $limit
     * @return list<array<string, mixed>>
     */
    public static function featured(int $limit = 4): array
    {
        return array_slice(self::products(), 0, max(1, $limit));
    }

    /**
     * @return list<array<string, string>> category cards
     */
    public static function categories(): array
    {
        return [
            ['title' => 'Топливные насосы', 'image' => '/images/home/categories/1.png', 'href' => '/catalog'],
            ['title' => 'Топливные модули', 'image' => '/images/home/categories/2.png', 'href' => '/catalog'],
            ['title' => 'Ремкомплекты', 'image' => '/images/home/categories/3.png', 'href' => '/catalog'],
            ['title' => 'Форсунки', 'image' => '/images/home/categories/4.png', 'href' => '/catalog'],
        ];
    }

    /**
     * Home page category grid — 12 items mirroring the static prototype
     * (blocks/home/categories.blade.php). Images are numbered by position
     * (/images/home/categories/{n}), links point to the catalog filtered
     * by the category slug. Kept separate from the header menu tree
     * (AppServiceProvider::catalogCategories): different shape and purpose.
     *
     * @return list<array{name: string, slug: string, image: string, href: string}>
     */
    public static function homeCategories(): array
    {
        $categories = [
            ['name' => 'Тормозная система', 'slug' => 'brake-system'],
            ['name' => 'Чип тюнинг', 'slug' => 'chip-tuning'],
            ['name' => 'Диски', 'slug' => 'wheels'],
            ['name' => 'Оптика', 'slug' => 'optics'],
            ['name' => 'Подвеска', 'slug' => 'suspension'],
            ['name' => 'Впускная система', 'slug' => 'intake'],
            ['name' => 'Приемные трубы и даунпайпы', 'slug' => 'downpipes'],
            ['name' => 'Выхлопные системы', 'slug' => 'exhaust'],
            ['name' => 'Карбоновые элементы', 'slug' => 'carbon'],
            ['name' => 'Масла и жидкости', 'slug' => 'oils'],
            ['name' => 'Топливная система', 'slug' => 'fuel-system'],
            ['name' => 'Охлаждение', 'slug' => 'cooling'],
        ];

        $result = [];

        foreach ($categories as $index => $category) {
            $result[] = [
                'name' => $category['name'],
                'slug' => $category['slug'],
                'image' => '/images/home/categories/'.($index + 1),
                'href' => route('catalog', ['category' => $category['slug']]),
            ];
        }

        return $result;
    }
}
