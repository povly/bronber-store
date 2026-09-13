<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use App\Support\PageBlocks\Concerns\BuildsLinkFields;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Text;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * Flexible-layouts block definitions for the header setting
 * (settings.value, key=header).
 *
 * Blocks mirror the static header markup: top-bar (phone + service links)
 * and main navigation. The logo is content (managed via the media
 * picker, SVG renders inline). Search, catalog menu, actions and the
 * language switcher stay in code — they are functional, not content.
 */
class HeaderBlockLibrary
{
    use BuildsLinkFields;

    /**
     * Block set for the header setting.
     *
     * An optional prebuilt field lets other libraries append their blocks
     * onto the same instance (see SettingFormPage's AJAX fallback).
     */
    public static function header(?FlexibleLayouts $layouts = null): FlexibleLayouts
    {
        $layouts ??= FlexibleLayouts::make('Значение', 'value');

        return $layouts
            ->block('logo', 'Логотип', [
                MediaManagerPicker::make('Изображение', 'image')
                    ->allowedExtensions(['svg', 'png', 'webp', 'avif', 'jpg', 'jpeg']),
            ], limit: 1, category: 'Шапка', description: 'Логотип шапки (SVG выводится инлайном; пусто — статический логотип)', icon: 'photo')
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
