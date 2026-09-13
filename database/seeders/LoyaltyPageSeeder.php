<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * Editable loyalty page: publishes the DB page with slug «loyalty» so
 * /loyalty renders from MoonShine-editable flexible-layout blocks.
 * Content mirrors the static prototype (blocks/loyalty/*); wording is
 * pulled from lang/{ru,en}/loyalty.php at seed time so the prototype
 * stays the single source of the texts (benefit texts keep the
 * prototype's <strong>/&nbsp; markup). Icons live in
 * public/images/loyalty/icons/, exported from the prototype inline SVG.
 * Idempotent: firstOrCreate by slug / (page_id, locale); an existing
 * translation with EMPTY content is filled with the demo data —
 * authored content is never overwritten (rule: .ai/rules/seeders.md).
 */
class LoyaltyPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::query()->firstOrCreate(
            ['slug' => 'loyalty'],
            ['is_published' => true, 'sort_order' => 0],
        );

        $locales = [];

        foreach (['ru', 'en'] as $locale) {
            $translation = PageTranslation::query()->firstOrCreate(
                ['page_id' => $page->getKey(), 'locale' => $locale],
                $this->attributes($locale),
            );

            if ($this->isEmptyContent($translation)) {
                $translation->fill($this->attributes($locale))->save();

                $locales[] = $locale.':filled';

                continue;
            }

            $locales[] = $locale.($translation->wasRecentlyCreated ? ':created' : ':exists');
        }

        Log::info('[LoyaltyPageSeeder] loyalty page seeded, locales={locales}', [
            'locales' => implode(', ', $locales),
        ]);
    }

    /**
     * Creation/fill attributes for a locale's demo translation.
     *
     * @return array<string, mixed>
     */
    private function attributes(string $locale): array
    {
        return [
            'title' => $locale === 'ru' ? 'Программа лояльности' : 'Loyalty Program',
            'meta_title' => $locale === 'ru'
                ? 'Программа лояльности — Bronber: бонусы за покупку автозапчастей'
                : 'Loyalty Program — Bronber: earn bonuses on auto parts orders',
            'meta_description' => $locale === 'ru'
                ? 'Программа лояльности Bronber: бонусы за каждую покупку автозапчастей для BMW, Audi, Volkswagen. 1 бонус = 1 ₽, оплата до 30% заказа бонусами, бонусы не сгорают.'
                : 'Bronber loyalty program: earn bonuses on every auto parts order for BMW, Audi, Volkswagen. 1 bonus = 1 ₽, pay up to 30% of your order with bonuses, bonuses never expire.',
            'content' => $this->content($locale),
        ];
    }

    /**
     * Loyalty blocks mirroring the static prototype
     * (blocks/loyalty/* + lang/{ru,en}/loyalty.php).
     *
     * @return list<array<string, mixed>>
     */
    private function content(string $locale): array
    {
        $icons = '/images/loyalty/icons/';

        $benefits = $locale === 'ru' ? [
            ['icon' => $icons.'benefits-coin.svg', 'title' => $this->text('benefit_1_title', $locale), 'text' => 'Бонусами можно оплатить до 30% от&nbsp;суммы заказа'],
            ['icon' => $icons.'benefits-cart.svg', 'title' => null, 'text' => '<strong>Бонусы</strong> начисляются с&nbsp;каждой покупки'],
            ['icon' => $icons.'benefits-calendar.svg', 'title' => null, 'text' => 'Бонусы <strong>не сгорают</strong> и&nbsp;действуют всегда'],
            ['icon' => $icons.'benefits-star.svg', 'title' => null, 'text' => '<strong>Эксклюзивные</strong> акции и&nbsp;предложения'],
        ] : [
            ['icon' => $icons.'benefits-coin.svg', 'title' => $this->text('benefit_1_title', $locale), 'text' => 'Pay up to 30% of your order with bonuses'],
            ['icon' => $icons.'benefits-cart.svg', 'title' => null, 'text' => '<strong>Bonuses</strong> awarded on every purchase'],
            ['icon' => $icons.'benefits-calendar.svg', 'title' => null, 'text' => 'Bonuses <strong>never expire</strong>'],
            ['icon' => $icons.'benefits-star.svg', 'title' => null, 'text' => '<strong>Exclusive</strong> promotions and offers'],
        ];

        $steps = [
            ['icon' => $icons.'how-cart.svg', 'title' => $this->text('step_1_title', $locale), 'text' => $this->text('step_1_text', $locale)],
            ['icon' => $icons.'how-coin.svg', 'title' => $this->text('step_2_title', $locale), 'text' => $this->text('step_2_text', $locale)],
            ['icon' => $icons.'how-card.svg', 'title' => $this->text('step_3_title', $locale), 'text' => $this->text('step_3_text', $locale)],
        ];

        $faqs = [
            ['question' => $this->text('faq_1_question', $locale), 'answer' => $this->text('faq_1_answer', $locale)],
            ['question' => $this->text('faq_2_question', $locale), 'answer' => $this->text('faq_2_answer', $locale)],
            ['question' => $this->text('faq_3_question', $locale), 'answer' => $this->text('faq_3_answer', $locale)],
            ['question' => $this->text('faq_4_question', $locale), 'answer' => $this->text('faq_4_answer', $locale)],
        ];

        return [
            [
                '_type' => 'loyalty-hero',
                'title' => $this->text('title', $locale),
                'text' => $this->text('subtitle', $locale),
                'label' => $this->text('register_button', $locale),
                'type' => 'custom',
                'page' => null,
                'url' => '#',
                'image_pc' => '/images/loyalty/1.png',
                'image_mb' => '/images/loyalty/1.png',
            ],
            [
                '_type' => 'loyalty-benefits',
                'items' => array_map(static fn (array $benefit): array => [
                    '_type' => 'item',
                    ...$benefit,
                ], $benefits),
            ],
            [
                '_type' => 'loyalty-how-works',
                'title' => $this->text('how_title', $locale),
                'items' => array_map(static fn (array $step): array => [
                    '_type' => 'item',
                    ...$step,
                ], $steps),
            ],
            [
                '_type' => 'loyalty-bottom',
                'title' => $this->text('example_title', $locale),
                'rows' => [
                    ['_type' => 'row', 'left_text' => $this->text('order_amount', $locale), 'right_text' => '10,000 ₽', 'color' => null],
                    ['_type' => 'row', 'left_text' => $this->text('bonus_accrued', $locale), 'right_text' => '100 ₽', 'color' => '#7212BC'],
                ],
                'gift_icon' => $icons.'bottom-gift.svg',
                'gift_title' => $this->text('banner_title', $locale),
                'gift_text' => $this->text('banner_text', $locale),
                'faq_title' => $this->text('faq_title', $locale),
                'faqs' => array_map(static fn (array $faq): array => [
                    '_type' => 'item',
                    ...$faq,
                ], $faqs),
            ],
        ];
    }

    /**
     * Prototype wording from the loyalty lang files of the locale.
     */
    private function text(string $key, string $locale): string
    {
        return (string) trans("loyalty.{$key}", [], $locale);
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
}
