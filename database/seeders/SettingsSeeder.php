<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\Languages\LanguageService;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Seed header/footer settings for every active language, pre-filled with
     * the same content as the static prototype markup (custom links to the
     * fixed prototype routes). Safe to re-run: upsert on (key, locale),
     * existing non-empty values are never overwritten.
     */
    public function run(): void
    {
        $languages = resolve(LanguageService::class)->codes();

        $existing = Setting::query()
            ->get(['key', 'locale'])
            ->map(static fn (Setting $setting): string => $setting->key.'.'.$setting->locale)
            ->all();

        foreach ($languages as $language) {
            $content = $this->content($language);

            foreach (['header', 'footer'] as $key) {
                $signature = $key.'.'.$language;

                if (! in_array($signature, $existing, true)) {
                    Setting::query()->create([
                        'key' => $key,
                        'locale' => $language,
                        'value' => $content[$key],
                    ]);

                    continue;
                }

                // Fill only empty values — admin edits are never overwritten.
                Setting::query()
                    ->where('key', $key)
                    ->where('locale', $language)
                    ->whereNull('value')
                    ->update(['value' => $content[$key]]);
            }
        }
    }

    /**
     * Default block content mirroring the static prototype for a locale.
     *
     * @return array{header: list<array<string, mixed>>, footer: list<array<string, mixed>>}
     */
    private function content(string $locale): array
    {
        $ru = $locale === 'ru';
        $t = static fn (string $ruText, string $enText): string => $ru ? $ruText : $enText;

        $prefix = $ru ? '' : '/en';

        $header = [
            [
                '_type' => 'top-bar',
                'phone' => '+7 (985) 449-8000',
                'links' => [
                    ['label' => $t('Доставка', 'Delivery'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/delivery'],
                    ['label' => $t('Гарантия', 'Warranty'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/returns'],
                    ['label' => $t('Вакансии', 'Careers'), 'type' => 'custom', 'page' => null, 'url' => '#!'],
                    ['label' => $t('Вопросы и ответы', 'FAQ'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/faq'],
                    ['label' => $t('Контакты', 'Contacts'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/contacts'],
                ],
            ],
            [
                '_type' => 'nav',
                'links' => [
                    ['label' => $t('Новинки', 'New'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/blog'],
                    ['label' => $t('Акции', 'Promo'), 'type' => 'custom', 'page' => null, 'url' => '#'],
                    ['label' => $t('Блог', 'Blog'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/blog'],
                    ['label' => $t('Бонусы', 'Bonus'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/loyalty'],
                    ['label' => $t('О нас', 'About'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/about'],
                ],
            ],
        ];

        $footer = [
            [
                '_type' => 'contacts',
                'items' => [
                    ['icon' => '/images/icons/phone.svg', 'text' => '+7 (985) 449-8000', 'href' => 'tel:+79854498000'],
                    ['icon' => '/images/icons/mail.svg', 'text' => 'info@bronber.ru', 'href' => 'mailto:info@bronber.ru'],
                ],
            ],
            [
                '_type' => 'socials',
                'links' => [
                    ['platform' => 'Instagram', 'url' => '#'],
                    ['platform' => 'YouTube', 'url' => '#'],
                ],
            ],
            [
                '_type' => 'links-column',
                'title' => $t('Каталог', 'Catalog'),
                'links' => [
                    ['label' => $t('Двигатель', 'Engine'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/catalog'],
                    ['label' => $t('Тормозная система', 'Brakes'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/catalog'],
                    ['label' => $t('Фильтры', 'Filters'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/catalog'],
                    ['label' => $t('Подвеска', 'Suspension'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/catalog'],
                    ['label' => $t('Электрика', 'Electric'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/catalog'],
                    ['label' => $t('Аксессуары', 'Accessories'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/catalog'],
                ],
            ],
            [
                '_type' => 'links-column',
                'title' => $t('Покупателям', 'For buyers'),
                'links' => [
                    ['label' => $t('Доставка', 'Delivery'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/delivery'],
                    ['label' => $t('Гарантия', 'Warranty'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/returns'],
                    ['label' => $t('Бонусная программа', 'Loyalty'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/loyalty'],
                    ['label' => $t('Вопросы и ответы', 'FAQ'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/faq'],
                ],
            ],
            [
                '_type' => 'links-column',
                'title' => $t('Компания', 'Company'),
                'links' => [
                    ['label' => $t('О нас', 'About'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/about'],
                    ['label' => $t('Блог', 'Blog'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/blog'],
                    ['label' => $t('Новости', 'News'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/blog'],
                    ['label' => $t('Вакансии', 'Careers'), 'type' => 'custom', 'page' => null, 'url' => '#'],
                    ['label' => $t('Контакты', 'Contacts'), 'type' => 'custom', 'page' => null, 'url' => $prefix.'/contacts'],
                ],
            ],
            [
                '_type' => 'bottom',
                'links' => [
                    ['label' => $t('Политика конфиденциальности', 'Privacy Policy'), 'type' => 'custom', 'page' => null, 'url' => '#'],
                    ['label' => $t('Пользовательское соглашение', 'Terms of Use'), 'type' => 'custom', 'page' => null, 'url' => '#'],
                ],
                'copyright' => $t('© 2026 Bronber Store. Все права защищены', '© 2026 Bronber Store. All rights reserved'),
                'developer_label' => $t('Разработка — Bronber', 'Developed by Bronber'),
                'developer_url' => '#!',
            ],
        ];

        return ['header' => $header, 'footer' => $footer];
    }
}
