<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * Editable contacts page: publishes the DB page with slug «contacts» so
 * /contacts renders from MoonShine-editable flexible-layout blocks and
 * is served by the catch-all route (no fixed GET route). Content mirrors
 * the static prototype (blocks/contacts/contacts.blade.php); wording is
 * pulled from lang/{ru,en}/store.php at seed time. Idempotent:
 * firstOrCreate by slug / (page_id, locale); an existing translation
 * with EMPTY content is filled with demo data — authored content is
 * never overwritten (rule: .ai/rules/seeders.md).
 */
class ContactsPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::query()->firstOrCreate(
            ['slug' => 'contacts'],
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

        Log::info('[ContactsPageSeeder] contacts page seeded, locales={locales}', [
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
            'title' => $locale === 'ru' ? 'Контакты' : 'Contacts',
            'meta_title' => $locale === 'ru'
                ? 'Контакты — Bronber: телефон, адрес и форма обращения'
                : 'Contacts — Bronber: phone, address and contact form',
            'meta_description' => $locale === 'ru'
                ? 'Контакты Bronber: телефон, email, адрес в Москве, Яндекс-карта и форма обращения — оставьте заявку, и наш менеджер свяжется с вами.'
                : 'Bronber contacts: phone, email, Moscow address, Yandex map and a contact form — leave a request and our manager will contact you.',
            'content' => $this->content($locale),
        ];
    }

    /**
     * Contacts blocks mirroring the static prototype
     * (blocks/contacts/contacts.blade.php + lang/{ru,en}/store.php).
     *
     * @return list<array<string, mixed>>
     */
    private function content(string $locale): array
    {
        return [
            [
                '_type' => 'contacts-main',
                'title' => $this->text('contacts_title', $locale),
                'subtitle' => $this->text('contacts_subtitle', $locale),
                'phone' => $this->text('contacts_phone', $locale),
                'email' => $this->text('contacts_email', $locale),
                'address' => $this->text('contacts_address', $locale),
                'map_html' => '<iframe src="https://yandex.ru/map-widget/v1/?ll=37.539822%2C55.748792&z=15" width="100%" height="100%" frameborder="0" loading="lazy"></iframe>',
                'submit_label' => '',
                'consent_text' => '',
                'success_message' => '',
            ],
        ];
    }

    /**
     * Prototype wording from the store lang file of the locale.
     */
    private function text(string $key, string $locale): string
    {
        return (string) trans("store.{$key}", [], $locale);
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
