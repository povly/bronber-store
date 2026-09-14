<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * Demo blog articles so /blog and /blog/{slug} render from the DB:
 * 6 published articles (3 unique × 2 — enough for the Alpine show-more),
 * newest first, with ru/en translations and flexible-layout blocks
 * (article-content / article-gallery / article-cta / article-related).
 *
 * Content mirrors the static prototype (blocks/article/article.blade.php
 * and the blog cards). Idempotent: firstOrCreate by slug /
 * (article_id, locale); an existing translation with EMPTY content is
 * filled with the demo data (e.g. a stub created from the admin panel)
 * — authored content is never overwritten.
 */
class BlogArticlesSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->articles() as $article) {
            $model = Article::query()->firstOrCreate(
                ['slug' => $article['slug']],
                [
                    'is_published' => true,
                    'published_at' => $article['published_at'],
                    'cover_pc' => $article['cover_pc'],
                    'cover_mb' => $article['cover_mb'],
                ],
            );

            $locales = [];

            foreach (['ru', 'en'] as $locale) {
                $translation = ArticleTranslation::query()->firstOrCreate(
                    ['article_id' => $model->getKey(), 'locale' => $locale],
                    $this->attributes($article, $locale),
                );

                if ($this->isEmptyContent($translation)) {
                    $translation->fill($this->attributes($article, $locale))->save();

                    $locales[] = $locale.':filled';

                    continue;
                }

                $locales[] = $locale.($translation->wasRecentlyCreated ? ':created' : ':exists');
            }

            Log::info('[BlogArticlesSeeder] slug={slug} seeded, locales={locales}', [
                'slug' => $article['slug'],
                'locales' => implode(', ', $locales),
            ]);
        }
    }

    /**
     * Creation/fill attributes for a locale's demo translation.
     *
     * @param  array<string, mixed>  $article
     * @return array<string, mixed>
     */
    private function attributes(array $article, string $locale): array
    {
        $translation = $article['translations'][$locale];

        return [
            'title' => $translation['title'],
            'tag' => $translation['tag'],
            'excerpt' => $translation['excerpt'],
            'meta_title' => $translation['title'].' — Bronber',
            'meta_description' => $translation['excerpt'],
            'content' => $this->content($article, $locale),
        ];
    }

    /**
     * A freshly created translation always carries content; a pre-existing
     * one is only "empty" when it has no blocks at all.
     */
    private function isEmptyContent(ArticleTranslation $translation): bool
    {
        return ! $translation->wasRecentlyCreated
            && ($translation->content === null || $translation->content === []);
    }

    /**
     * Article blocks: EditorJS text, CTA button, image gallery and (for
     * the featured article) the dynamic «Другие новости» section. The
     * CTA lives between the text and the gallery — the prototype placed
     * the button at the end of the article text, before the images.
     *
     * @param  array<string, mixed>  $article
     * @return list<array<string, mixed>>
     */
    private function content(array $article, string $locale): array
    {
        $blocks = [
            [
                '_type' => 'article-content',
                'text' => $this->editorJs($article, $locale),
            ],
            [
                '_type' => 'article-cta',
                'label' => $locale === 'ru' ? 'Перейти к направлению' : 'Go to division',
                'type' => 'page',
                'page' => 'about',
                'url' => null,
            ],
            [
                '_type' => 'article-gallery',
                'items' => array_map(
                    static fn (string $image): array => ['image' => $image],
                    $article['gallery'],
                ),
            ],
        ];

        if ($article['with_related']) {
            $blocks[] = [
                '_type' => 'article-related',
                'title' => $locale === 'ru' ? 'Другие новости' : 'Other news',
            ];
        }

        return $blocks;
    }

    /**
     * The article body as a single EditorJS document — same storage shape
     * as the admin editor (ReturnsPageSeeder): lead + subheaders + the
     * prototype paragraphs.
     *
     * @param  array<string, mixed>  $article
     */
    private function editorJs(array $article, string $locale): string
    {
        $translation = $article['translations'][$locale];

        /** @var list<array<string, mixed>> $blocks */
        $blocks = [
            ['type' => 'paragraph', 'data' => ['text' => $translation['lead']]],
            ['type' => 'header', 'data' => ['text' => $translation['heading_1'], 'level' => 2]],
        ];

        foreach ($translation['paragraphs'] as $paragraph) {
            $blocks[] = ['type' => 'paragraph', 'data' => ['text' => $paragraph]];
        }

        $blocks[] = ['type' => 'header', 'data' => ['text' => $translation['heading_2'], 'level' => 2]];

        foreach ($translation['closing'] as $paragraph) {
            $blocks[] = ['type' => 'paragraph', 'data' => ['text' => $paragraph]];
        }

        return (string) json_encode([
            'time' => 0,
            'blocks' => $blocks,
            'version' => '1.0.0',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Demo articles: 3 unique × 2, dates descending (newest first).
     *
     * @return list<array<string, mixed>>
     */
    private function articles(): array
    {
        $body = [
            'ru' => [
                'heading_1' => 'Профессиональный подход',
                'paragraphs' => [
                    'Bronber Auto Service — это профессиональный автосервис, созданный с учетом стандартов качества, сервиса и подхода, которые лежат в основе всех направлений бренда. Мы объединяем техническую экспертизу, современное оборудование и внимание к деталям, чтобы обеспечить высокий уровень обслуживания на каждом этапе работы с автомобилем.',
                    'Bronber Auto Service — это не стандартный автосервис в привычном понимании. В основе нового направления лежит системный подход, внимание к деталям и стремление создать сервисную среду, соответствующую ожиданиям владельцев автомобилей премиального сегмента.',
                    'Каждый этап работы — от диагностики до финальной передачи автомобиля клиенту — выстроен с учетом стандартов качества, прозрачности и комфорта.',
                ],
                'heading_2' => 'Новый этап развития экосистемы Bronber',
                'closing' => [
                    'Запуск Bronber Auto Service является важным шагом в развитии бренда и укрепляет концепцию единой экосистемы. Наша цель — создать не набор отдельных услуг, а целостную систему, в которой клиент получает высокий уровень сервиса, понятную коммуникацию и уверенность в результате.',
                    'Открытие нового направления позволяет нам предложить клиентам полный цикл услуг — от подбора и продажи запчастей до профессиональной установки и диагностики. Все работы выполняются с использованием оригинальных деталей и сертифицированных материалов.',
                    'Bronber Auto Service органично дополняет существующие направления и расширяет возможности бренда, сохраняя единый стиль, ценности и подход.',
                ],
            ],
            'en' => [
                'heading_1' => 'Professional approach',
                'paragraphs' => [
                    'Bronber Auto Service is a professional auto service center built around the quality, service and approach standards that underpin every division of the brand. We combine technical expertise, modern equipment and attention to detail to deliver a high level of service at every stage of working with your car.',
                    'Bronber Auto Service is not a conventional service center. The new division is built on a systematic approach, attention to detail and the drive to create a service environment that meets the expectations of premium car owners.',
                    'Every stage — from diagnostics to the final handover of the car — follows quality, transparency and comfort standards.',
                ],
                'heading_2' => 'A new stage in the Bronber ecosystem',
                'closing' => [
                    'The launch of Bronber Auto Service is an important step in the development of the brand and reinforces the concept of a single ecosystem. Our goal is not a set of separate services, but an integral system where the client receives a high level of service, clear communication and confidence in the result.',
                    'The opening of the new division allows us to offer clients a full cycle of services — from selecting and selling parts to professional installation and diagnostics. All work is performed using original parts and certified materials.',
                    'Bronber Auto Service complements the existing divisions and expands the capabilities of the brand, preserving a single style, values and approach.',
                ],
            ],
        ];

        $cards = [
            [
                'ru' => ['tag' => '#запуск', 'title' => 'Запуск нового направления BRONBER Auto Service', 'excerpt' => 'Профессиональный автосервис как часть единой экосистемы бренда', 'lead' => 'Bronber объявляет о запуске нового направления — Bronber Auto Service. Это логичный этап развития экосистемы бренда, объединяющий торговлю автозапчастями и профессиональный автосервис в единую систему обслуживания.'],
                'en' => ['tag' => '#launch', 'title' => 'Launch of the BRONBER Auto Service division', 'excerpt' => 'A professional auto service as part of the brand’s single ecosystem', 'lead' => 'Bronber announces the launch of a new division — Bronber Auto Service. This is a logical step in the development of our brand ecosystem, combining auto parts retail and professional auto service into a single service system.'],
            ],
            [
                'ru' => ['tag' => '#событие', 'title' => 'Открытие нового магазина автозапчастей', 'excerpt' => 'Более 50 000 наименований запчастей в наличии и под заказ', 'lead' => 'Мы открыли новый магазин автозапчастей Bronber. В наличии и под заказ — более 50 000 наименований для BMW, Audi и Volkswagen: от топливных насосов до оригинальных компонентов.'],
                'en' => ['tag' => '#event', 'title' => 'Opening of a new auto parts store', 'excerpt' => 'More than 50,000 parts in stock and on order', 'lead' => 'We have opened a new Bronber auto parts store. In stock and on order — more than 50,000 items for BMW, Audi and Volkswagen: from fuel pumps to original components.'],
            ],
            [
                'ru' => ['tag' => '#партнерство', 'title' => 'Новое партнерство с ведущими производителями', 'excerpt' => 'Теперь в ассортименте ещё больше оригинальных деталей', 'lead' => 'Bronber заключил новые партнерские соглашения с ведущими производителями автокомпонентов. Теперь в ассортименте ещё больше оригинальных деталей с подтвержденной документацией.'],
                'en' => ['tag' => '#partnership', 'title' => 'New partnership with leading manufacturers', 'excerpt' => 'Now even more genuine parts in our range', 'lead' => 'Bronber has signed new partnership agreements with leading auto component manufacturers. Our range now includes even more genuine parts with confirmed documentation.'],
            ],
        ];

        $meta = [
            ['slug' => 'zapusk-bronber-auto-service', 'published_at' => '2025-12-25', 'cover_pc' => '/images/blog/hero.jpg', 'cover_mb' => '/images/blog/hero-mb.jpg', 'with_related' => true, 'card' => 0],
            ['slug' => 'otkrytie-magazina-avtozapchastej', 'published_at' => '2025-12-18', 'cover_pc' => '/images/blog/2.jpg', 'cover_mb' => null, 'with_related' => false, 'card' => 1],
            ['slug' => 'partnerstvo-s-proizvoditelyami', 'published_at' => '2025-12-10', 'cover_pc' => '/images/blog/3.jpg', 'cover_mb' => null, 'with_related' => false, 'card' => 2],
            ['slug' => 'bronber-auto-service-ekosistema', 'published_at' => '2025-12-05', 'cover_pc' => '/images/blog/1.jpg', 'cover_mb' => null, 'with_related' => false, 'card' => 0],
            ['slug' => 'polnyj-cikl-uslug-bronber', 'published_at' => '2025-11-28', 'cover_pc' => '/images/blog/2.jpg', 'cover_mb' => null, 'with_related' => false, 'card' => 1],
            ['slug' => 'originalnye-detali-oficialnye-postavki', 'published_at' => '2025-11-21', 'cover_pc' => '/images/blog/3.jpg', 'cover_mb' => null, 'with_related' => false, 'card' => 2],
        ];

        foreach ($meta as &$article) {
            $article['gallery'] = ['/images/blog/1.jpg', '/images/blog/2.jpg', '/images/blog/3.jpg'];
            $article['translations'] = [
                'ru' => [...$cards[$article['card']]['ru'], ...$body['ru']],
                'en' => [...$cards[$article['card']]['en'], ...$body['en']],
            ];
        }

        return $meta;
    }
}
