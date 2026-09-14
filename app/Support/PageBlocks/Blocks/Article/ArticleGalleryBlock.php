<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Article;

use App\Support\PageBlocks\Blocks\PageBlock;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * «article-gallery» — слайдер изображений внутри статьи
 * (прототип: blocks/article-page). Рендерится вью
 * blocks/article/gallery.blade.php.
 */
final class ArticleGalleryBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('article-gallery', 'Статья — галерея (слайдер)', [
            FlexibleLayouts::make('Изображения', 'items')
                ->block('item', 'Изображение', [
                    MediaManagerPicker::make('Изображение', 'image')
                        ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'svg']),
                ]),
        ], category: 'Статья', description: 'Слайдер изображений, вставляемый между текстовыми блоками', icon: 'photo');
    }
}
