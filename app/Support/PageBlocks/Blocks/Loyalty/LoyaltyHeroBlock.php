<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Loyalty;

use App\Support\PageBlocks\Blocks\PageBlock;
use App\Support\PageBlocks\Concerns\BuildsLinkFields;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * «loyalty-hero» — hero программы лояльности: заголовок, описание,
 * кнопка-ссылка двух типов (страница сайта / кастомный URL —
 * {@see BuildsLinkFields}) и изображения для десктопа и мобильной
 * версии (прототип: blocks/loyalty/hero/hero.blade.php).
 */
final class LoyaltyHeroBlock implements PageBlock
{
    use BuildsLinkFields;

    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('loyalty-hero', 'Лояльность — hero', [
            Text::make('Заголовок', 'title')
                ->escapeOnApply(static fn (): bool => false),
            Textarea::make('Описание', 'text')
                ->escapeOnApply(static fn (): bool => false),
            ...self::linkFields(),
            MediaManagerPicker::make('Изображение (десктоп)', 'image_pc')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'svg', 'avif']),
            MediaManagerPicker::make('Изображение (мобильное)', 'image_mb')
                ->hint('Пусто — используется десктопное изображение')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'svg', 'avif']),
        ], limit: 1, category: 'Лояльность', description: 'Заголовок, описание, кнопка-ссылка и изображения (прототип /loyalty)', icon: 'photo');
    }
}
