<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Home;

use App\Support\PageBlocks\Blocks\PageBlock;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * «home-advs» — преимущества (карточки с изображением и текстом).
 */
final class HomeAdvsBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('home-advs', 'Преимущества (главная)', [
            FlexibleLayouts::make('Пункты', 'items')
                ->block('item', 'Пункт', [
                    Text::make('Заголовок', 'title'),
                    MediaManagerPicker::make('Изображение', 'image')
                        ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'svg']),
                    Textarea::make('Текст', 'text'),
                ]),
        ], category: 'Главная', description: 'Карточки преимуществ с изображением и текстом', icon: 'star');
    }
}
