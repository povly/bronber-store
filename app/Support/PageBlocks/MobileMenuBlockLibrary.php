<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use App\Support\PageBlocks\Concerns\BuildsLinkFields;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Text;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * Flexible-layouts block definitions for the mobile menu setting
 * (settings.value, key=mobile-menu).
 *
 * Blocks mirror the static drawer markup: navigation link lists (up to
 * three, separated by dividers), logo and contact items. The close button
 * and the language switcher stay in code — they are functional, not
 * content.
 */
class MobileMenuBlockLibrary
{
    use BuildsLinkFields;

    /**
     * Block set for the mobile menu setting.
     *
     * An optional prebuilt field lets other libraries append their blocks
     * onto the same instance (see SettingFormPage's AJAX fallback).
     */
    public static function mobileMenu(?FlexibleLayouts $layouts = null): FlexibleLayouts
    {
        $layouts ??= FlexibleLayouts::make('Значение', 'value');

        return $layouts
            ->block('links', 'Ссылки меню', [
                Json::make('Ссылки', 'links')
                    ->fields(self::linkFields()),
            ], limit: 3, category: 'Мобильное меню', description: 'Отдельный список ссылок (до 3: списки разделяются линиями)', icon: 'bars-3')
            ->block('logo', 'Логотип', [
                MediaManagerPicker::make('Изображение', 'image')
                    ->allowedExtensions(['svg', 'png', 'webp', 'avif', 'jpg', 'jpeg']),
            ], limit: 1, category: 'Мобильное меню', description: 'Логотип мобильного меню (SVG выводится инлайном; пусто — статический логотип)', icon: 'photo')
            ->block('contacts', 'Контакты', [
                Json::make('Пункты', 'items')
                    ->fields([
                        MediaManagerPicker::make('Иконка', 'icon')
                            ->allowedExtensions(['svg', 'png', 'webp', 'avif', 'jpg', 'jpeg']),
                        Text::make('Номер / текст', 'text')
                            ->required(),
                        Text::make('Ссылка (необязательно)', 'href')
                            ->hint('Например: tel:+7…, mailto:… или https://…'),
                    ]),
            ], limit: 1, category: 'Мобильное меню', description: 'Контакты внизу меню: иконка + номер + ссылка', icon: 'phone');
    }
}
