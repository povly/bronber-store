<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use App\Support\PageBlocks\Concerns\BuildsLinkFields;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Text;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;

/**
 * Flexible-layouts block definitions for the header setting
 * (settings.value, key=header).
 *
 * Blocks mirror the static header markup: top-bar (phone + service links)
 * and main navigation. Logo, search, catalog menu, actions and the language
 * switcher stay in code — they are functional, not content.
 */
class HeaderBlockLibrary
{
    use BuildsLinkFields;

    /**
     * Block set for the header setting.
     */
    public static function header(): FlexibleLayouts
    {
        return FlexibleLayouts::make('Значение', 'value')
            ->block('top-bar', 'Верхняя панель', [
                Text::make('Телефон', 'phone')
                    ->hint('Например: +7 (985) 449-80-00'),
                Json::make('Ссылки верхней панели', 'links')
                    ->fields(self::linkFields()),
            ], limit: 1, category: 'Шапка', description: 'Телефон и сервисные ссылки над основной шапкой', icon: 'phone')
            ->block('nav', 'Основное меню', [
                Json::make('Ссылки меню', 'links')
                    ->fields(self::linkFields()),
            ], limit: 1, category: 'Шапка', description: 'Ссылки основного меню (кнопка «Каталог» остаётся статической)', icon: 'bars-3');
    }
}
