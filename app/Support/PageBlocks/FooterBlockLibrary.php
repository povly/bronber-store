<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use App\Support\PageBlocks\Concerns\BuildsLinkFields;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Text;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;

/**
 * Flexible-layouts block definitions for the footer setting
 * (settings.value, key=footer).
 *
 * Blocks mirror the static footer markup: contacts, socials, link columns
 * (accordions) and the bottom legal row. Payment badges and the auth button
 * stay in code — they are functional, not content.
 */
class FooterBlockLibrary
{
    use BuildsLinkFields;

    /**
     * Block set for the footer setting.
     */
    public static function footer(): FlexibleLayouts
    {
        return FlexibleLayouts::make('Значение', 'value')
            ->block('contacts', 'Контакты', [
                Text::make('Телефон', 'phone'),
                Text::make('E-mail', 'email'),
            ], limit: 1, category: 'Подвал', description: 'Телефон и почта в блоке контактов', icon: 'phone')
            ->block('socials', 'Соцсети', [
                Json::make('Ссылки', 'links')
                    ->fields([
                        Text::make('Платформа', 'platform')
                            ->hint('Например: Instagram, YouTube, Telegram'),
                        Text::make('URL', 'url'),
                    ]),
            ], limit: 1, category: 'Подвал', description: 'Иконки соцсетей', icon: 'globe-alt')
            ->block('links-column', 'Колонка ссылок', [
                Text::make('Заголовок колонки', 'title'),
                Json::make('Ссылки', 'links')
                    ->fields(self::linkFields()),
            ], limit: 3, category: 'Подвал', description: 'Колонка-аккордеон со ссылками (до 3 колонок)', icon: 'link')
            ->block('bottom', 'Нижняя строка', [
                Text::make('Политика конфиденциальности — название', 'privacy_label'),
                Text::make('Политика конфиденциальности — URL', 'privacy_url'),
                Text::make('Условия использования — название', 'terms_label'),
                Text::make('Условия использования — URL', 'terms_url'),
                Text::make('Копирайт', 'copyright')
                    ->hint('Например: © 2026 Bronber Store'),
                Text::make('Разработчик — название', 'developer_label'),
                Text::make('Разработчик — URL', 'developer_url'),
            ], limit: 1, category: 'Подвал', description: 'Юридические ссылки, копирайт и разработчик', icon: 'cog-6-tooth');
    }
}
