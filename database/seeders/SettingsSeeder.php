<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\Languages\LanguageService;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Seed header/footer/mobile settings for every active language,
     * mirroring the live database content (includes the MoonShine admin
     * edits: logo/payment blocks, footer icons, picked link pages).
     * Safe to re-run: upsert on (key, locale), existing non-empty values
     * are never overwritten. The mobile-nav key is seeded as null — its
     * item icons are uploaded via the media manager, so the static
     * prototype keeps rendering until the admin fills the row.
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

            foreach (['header', 'footer', 'mobile-menu', 'mobile-nav', 'error-404'] as $key) {
                $signature = $key.'.'.$language;

                if (! in_array($signature, $existing, true)) {
                    Setting::query()->create([
                        'key' => $key,
                        'locale' => $language,
                        'value' => $content[$key],
                    ]);

                    continue;
                }

                // Fill only empty values — admin edits (and intentionally
                // empty keys like mobile-nav) are never overwritten.
                if ($content[$key] === null) {
                    continue;
                }

                Setting::query()
                    ->where('key', $key)
                    ->where('locale', $language)
                    ->whereNull('value')
                    ->update(['value' => $content[$key]]);
            }
        }
    }

    /**
     * Default block content for a locale.
     *
     * @return array{header: list<array<string, mixed>>, footer: list<array<string, mixed>>, mobile-menu: list<array<string, mixed>>, mobile-nav: null, error-404: list<array<string, mixed>>}
     */
    private function content(string $locale): array
    {
        return $locale === 'ru' ? $this->contentRu() : $this->contentEn();
    }

    /**
     * Russian settings blocks as stored in the database.
     *
     * @return array{header: list<array<string, mixed>>, footer: list<array<string, mixed>>, mobile-menu: list<array<string, mixed>>, mobile-nav: null, error-404: list<array<string, mixed>>}
     */
    private function contentRu(): array
    {
        $header = [
            [
                '_type' => 'top-bar',
                'phone' => '+7 (985) 449-8000',
                'links' => [
                    ['label' => 'Доставка', 'type' => 'custom', 'page' => 'index', 'url' => '/delivery'],
                    ['label' => 'Гарантия', 'type' => 'custom', 'page' => 'index', 'url' => '/returns'],
                    ['label' => 'Вакансии', 'type' => 'custom', 'page' => 'index', 'url' => '#!'],
                    ['label' => 'Вопросы и ответы', 'type' => 'custom', 'page' => 'index', 'url' => '/faq'],
                    ['label' => 'Контакты', 'type' => 'custom', 'page' => 'index', 'url' => '/contacts'],
                ],
            ],
            [
                '_type' => 'nav',
                'links' => [
                    ['label' => 'Новинки', 'type' => 'custom', 'page' => 'index', 'url' => '/blog'],
                    ['label' => 'Акции', 'type' => 'custom', 'page' => 'index', 'url' => '#'],
                    ['label' => 'Блог', 'type' => 'custom', 'page' => 'index', 'url' => '/blog'],
                    ['label' => 'Бонусы', 'type' => 'custom', 'page' => 'index', 'url' => '/loyalty'],
                    ['label' => 'О нас', 'type' => 'custom', 'page' => 'index', 'url' => '/about'],
                ],
            ],
            ['_type' => 'logo', 'image' => 'logo/bronber_eps-1.svg'],
        ];

        $footer = [
            [
                '_type' => 'contacts',
                'items' => [
                    ['icon' => 'footer/Frame-2.svg', 'text' => '+7 (985) 449-8000', 'href' => 'tel:+79854498000'],
                    ['icon' => 'footer/Frame-3.svg', 'text' => 'info@bronber.ru', 'href' => 'mailto:info@bronber.ru'],
                ],
            ],
            [
                '_type' => 'socials',
                'links' => [
                    ['platform' => 'Instagram', 'url' => '#', 'icon' => 'footer/Subtract.svg'],
                    ['platform' => 'YouTube', 'url' => '#', 'icon' => 'footer/youtube-svgrepo-com-1.svg'],
                ],
            ],
            [
                '_type' => 'links-column',
                'title' => 'Каталог',
                'links' => [
                    ['label' => 'Двигатель', 'type' => 'custom', 'page' => 'index', 'url' => '/catalog'],
                    ['label' => 'Тормозная система', 'type' => 'custom', 'page' => 'index', 'url' => '/catalog'],
                    ['label' => 'Фильтры', 'type' => 'custom', 'page' => 'index', 'url' => '/catalog'],
                    ['label' => 'Подвеска', 'type' => 'custom', 'page' => 'index', 'url' => '/catalog'],
                    ['label' => 'Электрика', 'type' => 'custom', 'page' => 'index', 'url' => '/catalog'],
                    ['label' => 'Аксессуары', 'type' => 'custom', 'page' => 'index', 'url' => '/catalog'],
                ],
            ],
            [
                '_type' => 'links-column',
                'title' => 'Покупателям',
                'links' => [
                    ['label' => 'Доставка', 'type' => 'custom', 'page' => 'index', 'url' => '/delivery'],
                    ['label' => 'Гарантия', 'type' => 'custom', 'page' => 'index', 'url' => '/returns'],
                    ['label' => 'Бонусная программа', 'type' => 'custom', 'page' => 'index', 'url' => '/loyalty'],
                    ['label' => 'Вопросы и ответы', 'type' => 'custom', 'page' => 'index', 'url' => '/faq'],
                ],
            ],
            [
                '_type' => 'links-column',
                'title' => 'Компания',
                'links' => [
                    ['label' => 'О нас', 'type' => 'custom', 'page' => 'index', 'url' => '/about'],
                    ['label' => 'Блог', 'type' => 'custom', 'page' => 'index', 'url' => '/blog'],
                    ['label' => 'Новости', 'type' => 'custom', 'page' => 'index', 'url' => '/blog'],
                    ['label' => 'Вакансии', 'type' => 'custom', 'page' => 'index', 'url' => '#'],
                    ['label' => 'Контакты', 'type' => 'custom', 'page' => 'index', 'url' => '/contacts'],
                ],
            ],
            [
                '_type' => 'bottom',
                'links' => [
                    ['label' => 'Политика конфиденциальности', 'type' => 'custom', 'page' => 'index', 'url' => '#'],
                    ['label' => 'Пользовательское соглашение', 'type' => 'custom', 'page' => 'index', 'url' => '#'],
                ],
                'copyright' => '© 2026 Bronber Store. Все права защищены',
                'developer_label' => 'Разработка — Bronber',
                'developer_url' => '#!',
            ],
            ['_type' => 'payment', 'image' => 'footer/Group-211.svg'],
        ];

        $mobileMenu = [
            [
                '_type' => 'links',
                'links' => [
                    ['label' => 'Новинки', 'type' => 'custom', 'page' => 'index', 'url' => '/blog'],
                    ['label' => 'Акции', 'type' => 'custom', 'page' => 'index', 'url' => '#'],
                    ['label' => 'Блог', 'type' => 'custom', 'page' => 'index', 'url' => '/blog'],
                    ['label' => 'Бонусы', 'type' => 'custom', 'page' => 'index', 'url' => '/loyalty'],
                    ['label' => 'О нас', 'type' => 'custom', 'page' => 'index', 'url' => '/about'],
                ],
            ],
            [
                '_type' => 'links',
                'links' => [
                    ['label' => 'Вакансии', 'type' => 'custom', 'page' => 'index', 'url' => '#!'],
                    ['label' => 'Вопросы и ответы', 'type' => 'custom', 'page' => 'index', 'url' => '/faq'],
                    ['label' => 'Контакты', 'type' => 'custom', 'page' => 'index', 'url' => '/contacts'],
                ],
            ],
            [
                '_type' => 'links',
                'links' => [
                    ['label' => 'Доставка', 'type' => 'custom', 'page' => 'index', 'url' => '/delivery'],
                    ['label' => 'Гарантия', 'type' => 'custom', 'page' => 'index', 'url' => '/returns'],
                ],
            ],
            [
                '_type' => 'contacts',
                'items' => [
                    ['icon' => 'footer/Frame-4.svg', 'text' => '+7 (985) 449-8000', 'href' => 'tel:+79854498000'],
                ],
            ],
        ];

        $error404 = [
            [
                '_type' => 'error-404',
                'title' => 'Страница не найдена',
                'text' => 'К сожалению, запрашиваемой страницы не существует или была перемещена.<br>Проверьте адрес или перейдите на главную страницу.',
                'buttons' => [
                    ['label' => 'На главную', 'type' => 'custom', 'page' => null, 'url' => '/', 'variant' => 'primary'],
                    ['label' => 'Перейти в каталог', 'type' => 'custom', 'page' => null, 'url' => '/catalog', 'variant' => 'white-border'],
                ],
            ],
        ];

        return ['header' => $header, 'footer' => $footer, 'mobile-menu' => $mobileMenu, 'mobile-nav' => null, 'error-404' => $error404];
    }

    /**
     * English settings blocks as stored in the database.
     *
     * @return array{header: list<array<string, mixed>>, footer: list<array<string, mixed>>, mobile-menu: list<array<string, mixed>>, mobile-nav: null, error-404: list<array<string, mixed>>}
     */
    private function contentEn(): array
    {
        $header = [
            [
                '_type' => 'top-bar',
                'phone' => '+7 (985) 449-8000',
                'links' => [
                    ['label' => 'Delivery', 'type' => 'custom', 'page' => null, 'url' => '/en/delivery'],
                    ['label' => 'Warranty', 'type' => 'custom', 'page' => null, 'url' => '/en/returns'],
                    ['label' => 'Careers', 'type' => 'custom', 'page' => null, 'url' => '#!'],
                    ['label' => 'FAQ', 'type' => 'custom', 'page' => null, 'url' => '/en/faq'],
                    ['label' => 'Contacts', 'type' => 'custom', 'page' => null, 'url' => '/en/contacts'],
                ],
            ],
            [
                '_type' => 'nav',
                'links' => [
                    ['label' => 'New', 'type' => 'custom', 'page' => null, 'url' => '/en/blog'],
                    ['label' => 'Promo', 'type' => 'custom', 'page' => null, 'url' => '#'],
                    ['label' => 'Blog', 'type' => 'custom', 'page' => null, 'url' => '/en/blog'],
                    ['label' => 'Bonus', 'type' => 'custom', 'page' => null, 'url' => '/en/loyalty'],
                    ['label' => 'About', 'type' => 'custom', 'page' => null, 'url' => '/en/about'],
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
                'title' => 'Catalog',
                'links' => [
                    ['label' => 'Engine', 'type' => 'custom', 'page' => null, 'url' => '/en/catalog'],
                    ['label' => 'Brakes', 'type' => 'custom', 'page' => null, 'url' => '/en/catalog'],
                    ['label' => 'Filters', 'type' => 'custom', 'page' => null, 'url' => '/en/catalog'],
                    ['label' => 'Suspension', 'type' => 'custom', 'page' => null, 'url' => '/en/catalog'],
                    ['label' => 'Electric', 'type' => 'custom', 'page' => null, 'url' => '/en/catalog'],
                    ['label' => 'Accessories', 'type' => 'custom', 'page' => null, 'url' => '/en/catalog'],
                ],
            ],
            [
                '_type' => 'links-column',
                'title' => 'For buyers',
                'links' => [
                    ['label' => 'Delivery', 'type' => 'custom', 'page' => null, 'url' => '/en/delivery'],
                    ['label' => 'Warranty', 'type' => 'custom', 'page' => null, 'url' => '/en/returns'],
                    ['label' => 'Loyalty', 'type' => 'custom', 'page' => null, 'url' => '/en/loyalty'],
                    ['label' => 'FAQ', 'type' => 'custom', 'page' => null, 'url' => '/en/faq'],
                ],
            ],
            [
                '_type' => 'links-column',
                'title' => 'Company',
                'links' => [
                    ['label' => 'About', 'type' => 'custom', 'page' => null, 'url' => '/en/about'],
                    ['label' => 'Blog', 'type' => 'custom', 'page' => null, 'url' => '/en/blog'],
                    ['label' => 'News', 'type' => 'custom', 'page' => null, 'url' => '/en/blog'],
                    ['label' => 'Careers', 'type' => 'custom', 'page' => null, 'url' => '#'],
                    ['label' => 'Contacts', 'type' => 'custom', 'page' => null, 'url' => '/en/contacts'],
                ],
            ],
            [
                '_type' => 'bottom',
                'links' => [
                    ['label' => 'Privacy Policy', 'type' => 'custom', 'page' => null, 'url' => '#'],
                    ['label' => 'Terms of Use', 'type' => 'custom', 'page' => null, 'url' => '#'],
                ],
                'copyright' => '© 2026 Bronber Store. All rights reserved',
                'developer_label' => 'Developed by Bronber',
                'developer_url' => '#!',
            ],
        ];

        $mobileMenu = [
            [
                '_type' => 'links',
                'links' => [
                    ['label' => 'New', 'type' => 'custom', 'page' => 'index', 'url' => '/en/blog'],
                    ['label' => 'Promo', 'type' => 'custom', 'page' => 'index', 'url' => '#'],
                    ['label' => 'Blog', 'type' => 'custom', 'page' => 'index', 'url' => '/en/blog'],
                    ['label' => 'Bonus', 'type' => 'custom', 'page' => 'index', 'url' => '/en/loyalty'],
                    ['label' => 'About', 'type' => 'custom', 'page' => 'index', 'url' => '/en/about'],
                ],
            ],
            [
                '_type' => 'links',
                'links' => [
                    ['label' => 'Careers', 'type' => 'custom', 'page' => 'index', 'url' => '#!'],
                    ['label' => 'FAQ', 'type' => 'custom', 'page' => 'index', 'url' => '/en/faq'],
                    ['label' => 'Contacts', 'type' => 'custom', 'page' => 'index', 'url' => '/en/contacts'],
                ],
            ],
            [
                '_type' => 'links',
                'links' => [
                    ['label' => 'Delivery', 'type' => 'custom', 'page' => 'index', 'url' => '/en/delivery'],
                    ['label' => 'Warranty', 'type' => 'custom', 'page' => 'index', 'url' => '/en/returns'],
                ],
            ],
            [
                '_type' => 'contacts',
                'items' => [
                    ['icon' => null, 'text' => '+7 (985) 449-8000', 'href' => 'tel:+79854498000'],
                ],
            ],
        ];

        $error404 = [
            [
                '_type' => 'error-404',
                'title' => 'Page not found',
                'text' => 'Unfortunately, the page you requested does not exist or has been moved.<br>Please check the address or go to the home page.',
                'buttons' => [
                    ['label' => 'Go home', 'type' => 'custom', 'page' => null, 'url' => '/en/', 'variant' => 'primary'],
                    ['label' => 'Go to catalog', 'type' => 'custom', 'page' => null, 'url' => '/en/catalog', 'variant' => 'white-border'],
                ],
            ],
        ];

        return ['header' => $header, 'footer' => $footer, 'mobile-menu' => $mobileMenu, 'mobile-nav' => null, 'error-404' => $error404];
    }
}
