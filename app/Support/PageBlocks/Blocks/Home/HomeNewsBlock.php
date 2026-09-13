<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Home;

use App\Support\PageBlocks\Blocks\PageBlock;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * «home-news» — карточки новостей с кнопкой «Все новости».
 */
final class HomeNewsBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('home-news', 'Новости (главная)', [
            Text::make('Заголовок секции', 'title')
                ->escapeOnApply(static fn (): bool => false),
            FlexibleLayouts::make('Новости', 'items')
                ->block('item', 'Новость', [
                    Text::make('Тег', 'tag')
                        ->escapeOnApply(static fn (): bool => false),
                    Text::make('Дата', 'date')
                        ->escapeOnApply(static fn (): bool => false),
                    Text::make('Заголовок', 'title')
                        ->escapeOnApply(static fn (): bool => false),
                    Textarea::make('Описание', 'desc')
                        ->escapeOnApply(static fn (): bool => false),
                    MediaManagerPicker::make('Изображение', 'image')
                        ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'svg']),
                ]),
        ], category: 'Главная', description: 'Карточки новостей + кнопка «Все новости»', icon: 'newspaper');
    }
}
