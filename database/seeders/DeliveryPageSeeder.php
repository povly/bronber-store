<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * Editable delivery page: publishes the DB page with slug «delivery» so
 * /delivery renders from MoonShine-editable flexible-layout blocks.
 * Content mirrors the static prototype (blocks/delivery/delivery.blade.php);
 * the wording is pulled from lang/{ru,en}/delivery.php at seed time so the
 * prototype stays the single source of the texts. Idempotent:
 * firstOrCreate by slug / (page_id, locale); an existing translation with
 * EMPTY content is filled with the demo data — authored content is never
 * overwritten (rule: .ai/rules/seeders.md).
 */
class DeliveryPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::query()->firstOrCreate(
            ['slug' => 'delivery'],
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

        Log::info('[DeliveryPageSeeder] delivery page seeded, locales={locales}', [
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
            'title' => $locale === 'ru' ? 'Доставка и оплата' : 'Delivery & Payment',
            'meta_title' => $locale === 'ru'
                ? 'Доставка и оплата — Bronber: способы оплаты заказов автозапчастей'
                : 'Delivery & Payment — Bronber: auto parts order payment methods',
            'meta_description' => $locale === 'ru'
                ? 'Способы оплаты заказов автозапчастей Bronber: наличными при самовывозе, перевод на банковскую карту, оплата по СБП через QR-код, оплата в других валютах. Контакты для вопросов доставки и оплаты.'
                : 'Bronber auto parts payment methods: cash on pickup, bank card transfer, SBP QR-code payment, payment in other currencies. Contacts for delivery and payment inquiries.',
            'content' => $this->content($locale),
        ];
    }

    /**
     * Delivery blocks mirroring the static prototype
     * (blocks/delivery/delivery.blade.php + lang/{ru,en}/delivery.php).
     *
     * @return list<array<string, mixed>>
     */
    private function content(string $locale): array
    {
        // Media-manager icons as stored in the database: real uploads for
        // the default locale, empty strings on non-default translations
        // (rendered via the locale media fallback).
        $methodIcons = $locale === 'ru'
            ? ['delivery/cash.svg', 'delivery/card.svg', 'delivery/sbp.svg', 'delivery/Frame-5.svg']
            : ['', '', '', ''];

        $contactIcons = $locale === 'ru'
            ? ['delivery/phone.svg', 'delivery/email.svg']
            : ['', ''];

        $methods = [
            ['title' => $this->text('method_cash_title', $locale), 'icon' => $methodIcons[0], 'text' => $this->text('method_cash_text', $locale)],
            ['title' => $this->text('method_card_title', $locale), 'icon' => $methodIcons[1], 'text' => $this->text('method_card_text', $locale)],
            ['title' => $this->text('method_sbp_title', $locale), 'icon' => $methodIcons[2], 'text' => $this->text('method_sbp_text', $locale)],
            ['title' => $this->text('method_currency_title', $locale), 'icon' => $methodIcons[3], 'text' => $this->text('method_currency_text', $locale)],
        ];

        $phone = $this->text('contact_phone', $locale);
        $email = $this->text('contact_email', $locale);

        return [
            [
                '_type' => 'delivery-methods',
                'title' => $this->text('title', $locale),
                'items' => array_map(static fn (array $method): array => [
                    '_type' => 'item',
                    ...$method,
                ], $methods),
            ],
            [
                '_type' => 'contact-list',
                'title' => $this->text('contact_title', $locale),
                'items' => [
                    ['_type' => 'item', 'text' => $phone, 'href' => 'tel:'.preg_replace('/[^+\d]/', '', $phone), 'icon' => $contactIcons[0]],
                    ['_type' => 'item', 'text' => $email, 'href' => 'mailto:'.$email, 'icon' => $contactIcons[1]],
                ],
            ],
        ];
    }

    /**
     * Prototype wording from the delivery lang files of the locale.
     */
    private function text(string $key, string $locale): string
    {
        return (string) trans("delivery.{$key}", [], $locale);
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
