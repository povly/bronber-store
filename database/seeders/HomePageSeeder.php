<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * Editable home page: publishes the DB page with slug «index» so the
 * site root renders from MoonShine-editable flexible-layout blocks.
 * Content mirrors the static prototype (blocks/home/*). Idempotent:
 * firstOrCreate by slug / (page_id, locale).
 */
class HomePageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::query()->firstOrCreate(
            ['slug' => 'index'],
            ['is_published' => true, 'sort_order' => 0],
        );

        $locales = [];

        foreach (['ru' => $this->contentRu(), 'en' => $this->contentEn()] as $locale => $content) {
            $translation = PageTranslation::query()->firstOrCreate(
                ['page_id' => $page->getKey(), 'locale' => $locale],
                [
                    'title' => $locale === 'ru'
                        ? 'BRONBER — автозапчасти для BMW, Audi, VW'
                        : 'BRONBER — Auto parts for BMW, Audi, VW',
                    'meta_title' => $locale === 'ru'
                        ? 'BRONBER — автозапчасти: топливные насосы и компоненты для BMW, Audi, VW'
                        : 'BRONBER — Auto parts: fuel pumps and components for BMW, Audi, VW',
                    'meta_description' => $locale === 'ru'
                        ? 'Интернет-магазин автозапчастей: топливные насосы и компоненты для BMW, Audi, Volkswagen. Оригиналы и сертифицированные аналоги.'
                        : 'Auto parts store: fuel pumps and components for BMW, Audi, Volkswagen. Originals and certified analogues.',
                    'content' => $content,
                ],
            );

            $locales[] = $locale.($translation->wasRecentlyCreated ? ':created' : ':exists');
        }

        Log::info('[HomePageSeeder] index page seeded, locales={locales}', [
            'locales' => implode(', ', $locales),
        ]);
    }

    /**
     * Home blocks mirroring the static prototype (blocks/home/*).
     *
     * @return list<array<string, mixed>>
     */
    private function contentRu(): array
    {
        return [
            [
                '_type' => 'home-hero',
                'slides' => [
                    ['_type' => 'slide', 'title' => 'Найдите нужные товары по категориям', 'text' => "Удобная структура каталога поможет быстро перейти к\u{00A0}нужному разделу и\u{00A0}выбрать подходящий товар", 'btn_text' => 'В каталог', 'btn_href' => '/catalog'],
                    ['_type' => 'slide', 'title' => 'Найдите нужные товары', 'text' => "Удобная структура каталога поможет быстро перейти к\u{00A0}нужному разделу", 'btn_text' => 'В каталог', 'btn_href' => '/catalog'],
                    ['_type' => 'slide', 'title' => 'Найдите нужные товары', 'text' => "Удобная структура каталога поможет быстро перейти к\u{00A0}нужному разделу", 'btn_text' => 'В каталог', 'btn_href' => '/catalog'],
                ],
            ],
            ['_type' => 'home-categories', 'title' => 'Категории'],
            [
                '_type' => 'home-advs',
                'items' => [
                    ['_type' => 'item', 'title' => 'Быстрая доставка', 'image' => '/images/home/advs/1.svg', 'text' => "Отправка заказов в\u{00A0}течение 24 часов"],
                    ['_type' => 'item', 'title' => 'Удобная оплата', 'image' => '/images/home/advs/2.svg', 'text' => 'Оплата картой или при получении'],
                    ['_type' => 'item', 'title' => 'Возврат товара', 'image' => '/images/home/advs/3.svg', 'text' => '14 дней на возврат без проблем'],
                    ['_type' => 'item', 'title' => 'Гарантия качества', 'image' => '/images/home/advs/4.svg', 'text' => 'Только проверенные автозапчасти'],
                ],
            ],
            ['_type' => 'home-products', 'title' => 'Рекомендованные товары', 'count' => 4],
            ['_type' => 'home-products', 'title' => 'Топливные насосы', 'count' => 4],
            ['_type' => 'home-products', 'title' => 'Тормозные диски', 'count' => 4],
            [
                '_type' => 'home-partners',
                'title' => 'Наши партнеры',
                'images' => $this->partnerLogos(),
            ],
            [
                '_type' => 'home-news',
                'title' => 'Новости',
                'items' => [
                    ['_type' => 'item', 'tag' => '#запуск', 'date' => '25/12/25', 'title' => 'Запуск нового направления BRONBER Auto Service', 'desc' => 'Открываем новое направление в сфере автосервиса. Современное оборудование, квалифицированные специалисты и широкий спектр услуг для вашего автомобиля.', 'image' => '/images/blog/1.jpg'],
                    ['_type' => 'item', 'tag' => '#событие', 'date' => '18/12/25', 'title' => 'Открытие нового магазина автозапчастей', 'desc' => 'Рады сообщить об открытии нового магазина. Более 50 000 наименований запчастей в наличии и под заказ с быстрой доставкой.', 'image' => '/images/blog/2.jpg'],
                    ['_type' => 'item', 'tag' => '#партнерство', 'date' => '10/12/25', 'title' => 'Новое партнерство с ведущими производителями', 'desc' => 'Заключили соглашения с крупнейшими мировыми производителями автозапчастей. Теперь в ассортименте ещё больше оригинальных деталей.', 'image' => '/images/blog/3.jpg'],
                ],
            ],
        ];
    }

    /**
     * English translation of the same home blocks.
     *
     * @return list<array<string, mixed>>
     */
    private function contentEn(): array
    {
        return [
            [
                '_type' => 'home-hero',
                'slides' => [
                    ['_type' => 'slide', 'title' => 'Find the right parts by category', 'text' => "A\u{00A0}convenient catalog structure takes you straight to\u{00A0}the section you need and\u{00A0}helps you pick the\u{00A0}right part", 'btn_text' => 'To catalog', 'btn_href' => '/en/catalog'],
                    ['_type' => 'slide', 'title' => 'Find the right parts', 'text' => "A\u{00A0}convenient catalog structure takes you straight to\u{00A0}the section you need", 'btn_text' => 'To catalog', 'btn_href' => '/en/catalog'],
                    ['_type' => 'slide', 'title' => 'Find the right parts', 'text' => "A\u{00A0}convenient catalog structure takes you straight to\u{00A0}the section you need", 'btn_text' => 'To catalog', 'btn_href' => '/en/catalog'],
                ],
            ],
            ['_type' => 'home-categories', 'title' => 'Categories'],
            // Item media is owned by the default locale: empty media fields
            // of non-default translations inherit the default's value at
            // render time (App\Support\PageBlocks\MediaFallback).
            [
                '_type' => 'home-advs',
                'items' => [
                    ['_type' => 'item', 'title' => 'Fast delivery', 'text' => 'Orders shipped within 24 hours'],
                    ['_type' => 'item', 'title' => 'Easy payment', 'text' => 'Pay by card or on delivery'],
                    ['_type' => 'item', 'title' => 'Returns', 'text' => '14 days for hassle-free returns'],
                    ['_type' => 'item', 'title' => 'Quality guarantee', 'text' => 'Only proven auto parts'],
                ],
            ],
            ['_type' => 'home-products', 'title' => 'Recommended products', 'count' => 4],
            ['_type' => 'home-products', 'title' => 'Fuel pumps', 'count' => 4],
            ['_type' => 'home-products', 'title' => 'Brake discs', 'count' => 4],
            // Media is owned by the default locale: empty media fields of
            // non-default translations inherit the default's value at
            // render time (App\Support\PageBlocks\MediaFallback).
            ['_type' => 'home-partners', 'title' => 'Our partners'],
            [
                '_type' => 'home-news',
                'title' => 'News',
                'items' => [
                    ['_type' => 'item', 'tag' => '#launch', 'date' => '25/12/25', 'title' => 'BRONBER Auto Service: launching a new direction', 'desc' => 'We are opening a new auto service direction. Modern equipment, qualified specialists and a wide range of services for your car.', 'image' => '/images/blog/1.jpg'],
                    ['_type' => 'item', 'tag' => '#event', 'date' => '18/12/25', 'title' => 'Opening a new auto parts store', 'desc' => 'Happy to announce the opening of a new store. More than 50,000 parts in stock and on order with fast delivery.', 'image' => '/images/blog/2.jpg'],
                    ['_type' => 'item', 'tag' => '#partnership', 'date' => '10/12/25', 'title' => 'New partnership with leading manufacturers', 'desc' => 'We have signed agreements with the world\'s largest auto parts manufacturers. Now even more genuine parts in our range.', 'image' => '/images/blog/3.jpg'],
                ],
            ],
        ];
    }

    /**
     * Partner logo slides (brembo/bosch/akrapovic repeated 5x, as in the prototype).
     *
     * @return list<string>
     */
    private function partnerLogos(): array
    {
        $logos = [
            '/images/home/partners/brembo.png',
            '/images/home/partners/bosch.png',
            '/images/home/partners/akrapovic.png',
        ];

        $images = [];

        for ($round = 0; $round < 5; $round++) {
            foreach ($logos as $logo) {
                $images[] = $logo;
            }
        }

        return $images;
    }
}
