<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Home;

use App\Support\PageBlocks\Blocks\PageBlock;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;

/**
 * «home-hero» — промо-слайдер главной страницы
 * (прототип: blocks/home/hero.blade.php).
 */
final class HomeHeroBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('home-hero', 'Промо-слайдер (главная)', [
            FlexibleLayouts::make('Слайды', 'slides')
                ->block('slide', 'Слайд', [
                    Text::make('Заголовок', 'title')
                        ->escapeOnApply(static fn (): bool => false),
                    Textarea::make('Текст', 'text')
                        ->escapeOnApply(static fn (): bool => false),
                    Text::make('Текст кнопки', 'btn_text')
                        ->escapeOnApply(static fn (): bool => false),
                    Text::make('Ссылка кнопки', 'btn_href')
                        ->escapeOnApply(static fn (): bool => false),
                ]),
        ], limit: 1, category: 'Главная', description: 'Слайдер промо-баннеров с кнопкой (прототип главной)', icon: 'photo');
    }
}
