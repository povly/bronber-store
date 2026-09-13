<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * Editable about page: publishes the DB page with slug «about» so /about
 * renders from MoonShine-editable flexible-layout blocks. Content mirrors
 * the static prototype timeline (blocks/about/about.blade.php). Idempotent:
 * firstOrCreate by slug / (page_id, locale); an existing translation
 * with EMPTY content is filled with the demo data (e.g. a stub created
 * from the admin panel) — authored content is never overwritten.
 */
class AboutPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::query()->firstOrCreate(
            ['slug' => 'about'],
            ['is_published' => true, 'sort_order' => 0],
        );

        $locales = [];

        foreach (['ru' => $this->contentRu(), 'en' => $this->contentEn()] as $locale => $content) {
            $translation = PageTranslation::query()->firstOrCreate(
                ['page_id' => $page->getKey(), 'locale' => $locale],
                $this->attributes($locale, $content),
            );

            if ($this->isEmptyContent($translation)) {
                $translation->fill($this->attributes($locale, $content))->save();

                $locales[] = $locale.':filled';

                continue;
            }

            $locales[] = $locale.($translation->wasRecentlyCreated ? ':created' : ':exists');
        }

        Log::info('[AboutPageSeeder] about page seeded, locales={locales}', [
            'locales' => implode(', ', $locales),
        ]);
    }

    /**
     * Creation/fill attributes for a locale's demo translation.
     *
     * @param  list<array<string, mixed>>  $content
     * @return array<string, mixed>
     */
    private function attributes(string $locale, array $content): array
    {
        return [
            'title' => $locale === 'ru' ? 'О компании' : 'About Us',
            'meta_title' => $locale === 'ru'
                ? 'О компании Bronber — автозапчасти для BMW, Audi, VW с 2017 года'
                : 'About Bronber — auto parts for BMW, Audi, VW since 2017',
            'meta_description' => $locale === 'ru'
                ? 'История компании Bronber: от небольшого магазина топливных насосов до надёжного поставщика автозапчастей для BMW, Audi и Volkswagen. Оригиналы и сертифицированные аналоги.'
                : 'The history of Bronber: from a small fuel pump store to a trusted supplier of auto parts for BMW, Audi and Volkswagen. Originals and certified analogues.',
            'content' => $content,
        ];
    }

    /**
     * A freshly created translation always carries content; a pre-existing
     * one is only "empty" when it has no blocks at all.
     */
    private function isEmptyContent(PageTranslation $translation): bool
    {
        return ! $translation->wasRecentlyCreated
            && ($translation->content === null || $translation->content === []);
    }

    /**
     * Timeline blocks mirroring the static prototype (blocks/about/about.blade.php).
     *
     * @return list<array<string, mixed>>
     */
    private function contentRu(): array
    {
        return [
            [
                '_type' => 'about-timeline',
                'title' => 'История компании',
                'items' => [
                    ['_type' => 'item', 'title_desktop' => '2017', 'title_mobile' => '2017', 'text' => $this->editorJs(
                        'Компания Bronber открыла первый магазин автозапчастей для BMW, Audi и Volkswagen.',
                        'В ассортименте — топливные насосы и компоненты топливной системы напрямую от официальных дистрибьюторов.'
                    )],
                    ['_type' => 'item', 'title_desktop' => '2018', 'title_mobile' => '2018', 'text' => $this->editorJs(
                        'Расширили склад и запустили сервис подбора запчастей по VIN-коду.',
                        'Клиенты получили возможность заказывать редкие позиции под конкретный автомобиль без долгого ожидания.'
                    )],
                    ['_type' => 'item', 'title_desktop' => '2019', 'title_mobile' => '2019', 'text' => $this->editorJs(
                        'Запустили онлайн-каталог на сайте: поиск по артикулу, характеристики и совместимость деталей.',
                        'Заказы стало можно оформлять круглосуточно из любого региона.'
                    )],
                    ['_type' => 'item', 'title_desktop' => '2020', 'title_mobile' => '2020', 'text' => $this->editorJs(
                        'Начали отправлять заказы по всей России транспортными компаниями.',
                        'Добавили оплату через СБП и банковские карты; доставка в регионы занимает от 3 до 7 рабочих дней.'
                    )],
                    ['_type' => 'item', 'title_desktop' => '2021', 'title_mobile' => '2021', 'text' => $this->editorJs(
                        'Более 10 000 клиентов купили запчасти в Bronber.',
                        'Запустили программу лояльности с бонусами за покупки и отзывы.'
                    )],
                ],
            ],
        ];
    }

    /**
     * English translation of the same timeline blocks.
     *
     * @return list<array<string, mixed>>
     */
    private function contentEn(): array
    {
        return [
            [
                '_type' => 'about-timeline',
                'title' => 'Our Story',
                'items' => [
                    ['_type' => 'item', 'title_desktop' => '2017', 'title_mobile' => '2017', 'text' => $this->editorJs(
                        'Bronber opened its first auto parts store for BMW, Audi and Volkswagen.',
                        'The catalogue featured fuel pumps and fuel system components supplied directly by official distributors.'
                    )],
                    ['_type' => 'item', 'title_desktop' => '2018', 'title_mobile' => '2018', 'text' => $this->editorJs(
                        'We expanded our warehouse and launched a VIN-based parts lookup service.',
                        'Customers could order rare parts for their exact car without a long wait.'
                    )],
                    ['_type' => 'item', 'title_desktop' => '2019', 'title_mobile' => '2019', 'text' => $this->editorJs(
                        'We launched the online catalogue: search by article number, specifications and parts compatibility.',
                        'Orders could now be placed around the clock from any region.'
                    )],
                    ['_type' => 'item', 'title_desktop' => '2020', 'title_mobile' => '2020', 'text' => $this->editorJs(
                        'We started shipping orders across Russia with transport companies.',
                        'SBP and bank card payments were added; delivery to the regions takes 3 to 7 business days.'
                    )],
                    ['_type' => 'item', 'title_desktop' => '2021', 'title_mobile' => '2021', 'text' => $this->editorJs(
                        'More than 10,000 customers have bought parts from Bronber.',
                        'We launched a loyalty program with bonuses for purchases and reviews.'
                    )],
                ],
            ],
        ];
    }

    /**
     * EditorJs payload for a list of paragraph texts — the format stored
     * by the admin editor (same shape as DemoPageSeeder).
     */
    private function editorJs(string ...$paragraphs): string
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
}
