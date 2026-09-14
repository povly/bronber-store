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
 * stays the single source of the texts. Benefits/how-works texts are
 * stored as EditorJS documents (single paragraph block) — the block
 * views render them via RenderEditorJs. Icons live in
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
        // Benefits/how-works texts are EditorJS documents (RenderEditorJs
        // in the block views) — a single paragraph block carries the
        // prototype wording incl. <strong>/&nbsp; markup.
        $editorjs = static fn (string $html): string => (string) json_encode(
            ['time' => 0, 'blocks' => [['type' => 'paragraph', 'data' => ['text' => $html]]], 'version' => '1.0.0'],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        );

        // Media-manager icons as stored in the database: re-uploaded for
        // the default locale, empty strings on non-default translations
        // where the locale media fallback applies (how-works/gift icons
        // keep their prototype paths on en — the database stores them so).
        $heroImage = $locale === 'ru' ? 'loyalty/1.png' : '';
        $giftIcon = $locale === 'ru' ? 'loyalty/bottom-gift.svg' : '/images/loyalty/icons/bottom-gift.svg';

        $benefits = $locale === 'ru' ? [
            ['icon' => 'loyalty/Frame-6.svg', 'title' => $this->text('benefit_1_title', $locale), 'text' => $editorjs('Бонусами можно оплатить до 30% от&nbsp;суммы заказа')],
            ['icon' => 'loyalty/Frame-7.svg', 'title' => null, 'text' => $editorjs('<strong>Бонусы</strong> начисляются с&nbsp;каждой покупки')],
            ['icon' => 'loyalty/Frame-8.svg', 'title' => null, 'text' => $editorjs('Бонусы <strong>не сгорают</strong> и&nbsp;действуют всегда')],
            ['icon' => 'loyalty/Frame-9.svg', 'title' => null, 'text' => $editorjs('<strong>Эксклюзивные</strong> акции и&nbsp;предложения')],
        ] : [
            ['icon' => '', 'title' => $this->text('benefit_1_title', $locale), 'text' => $editorjs('Pay up to 30% of your order with bonuses')],
            ['icon' => '', 'title' => null, 'text' => $editorjs('<strong>Bonuses</strong> awarded on every purchase')],
            ['icon' => '', 'title' => null, 'text' => $editorjs('Bonuses <strong>never expire</strong>')],
            ['icon' => '', 'title' => null, 'text' => $editorjs('<strong>Exclusive</strong> promotions and offers')],
        ];

        $stepIcons = $locale === 'ru'
            ? ['loyalty/how-cart.svg', 'loyalty/how-coin.svg', 'loyalty/how-card.svg']
            : ['/images/loyalty/icons/how-cart.svg', '/images/loyalty/icons/how-coin.svg', '/images/loyalty/icons/how-card.svg'];

        $steps = [
            ['icon' => $stepIcons[0], 'title' => $this->text('step_1_title', $locale), 'text' => $editorjs($this->text('step_1_text', $locale))],
            ['icon' => $stepIcons[1], 'title' => $this->text('step_2_title', $locale), 'text' => $editorjs($this->text('step_2_text', $locale))],
            ['icon' => $stepIcons[2], 'title' => $this->text('step_3_title', $locale), 'text' => $editorjs($this->text('step_3_text', $locale))],
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
                'image_pc' => $heroImage,
                'image_mb' => $heroImage,
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
                'gift_icon' => $giftIcon,
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
