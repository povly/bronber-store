<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use App\Support\PageBlocks\Concerns\BuildsLinkFields;
use MoonShine\UI\Fields\Json;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * Flexible-layouts block definitions for the mobile bottom nav setting
 * (settings.value, key=mobile-nav).
 *
 * Each item is an icon + a two-type link + a label («Название» doubles
 * as the caption under the icon). The catalog button stays in code —
 * it opens the catalog menu, which a link cannot express; it renders
 * right after the first configured item (position 2, as in the
 * static prototype).
 */
class MobileNavBlockLibrary
{
    use BuildsLinkFields;

    /**
     * Block set for the mobile nav setting.
     *
     * An optional prebuilt field lets other libraries append their blocks
     * onto the same instance (see SettingFormPage's AJAX fallback).
     */
    public static function mobileNav(?FlexibleLayouts $layouts = null): FlexibleLayouts
    {
        $layouts ??= FlexibleLayouts::make('Значение', 'value');

        return $layouts
            ->block('items', 'Пункты панели', [
                Json::make('Пункты', 'items')
                    ->fields([
                        MediaManagerPicker::make('Иконка', 'icon')
                            ->allowedExtensions(['svg', 'png', 'webp', 'avif', 'jpg', 'jpeg']),
                        ...self::linkFields(),
                    ])
                    ->hint('«Название» — подпись под иконкой; кнопка «Каталог» функциональная и всегда вторая'),
            ], limit: 1, category: 'Мобильная панель', description: 'Нижняя мобильная панель: иконка + двухтипная ссылка + подпись', icon: 'device-phone-mobile');
    }
}
