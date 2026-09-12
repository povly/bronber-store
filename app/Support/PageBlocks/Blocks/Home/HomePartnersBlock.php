<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Home;

use App\Support\PageBlocks\Blocks\PageBlock;
use MoonShine\UI\Fields\Text;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * «home-partners» — логотипы партнёров (лента-слайдер).
 */
final class HomePartnersBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('home-partners', 'Партнёры (главная)', [
            Text::make('Заголовок секции', 'title'),
            MediaManagerPicker::make('Логотипы', 'images')
                ->multiple()
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'svg']),
        ], category: 'Главная', description: 'Лента логотипов партнёров', icon: 'building-office-2');
    }
}
