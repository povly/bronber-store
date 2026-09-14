<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Article;

use App\Support\PageBlocks\Blocks\PageBlock;
use App\Support\PageBlocks\Concerns\BuildsLinkFields;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;

/**
 * «article-cta» — кнопка-призыв внутри статьи: двухтиповая
 * ссылка «страница сайта / кастомный URL»
 * ({@see BuildsLinkFields}; прототип: blocks/article-page).
 */
final class ArticleCtaBlock implements PageBlock
{
    use BuildsLinkFields;

    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('article-cta', 'Статья — кнопка (CTA)', [
            ...self::linkFields(),
        ], limit: 1, category: 'Статья', description: 'Кнопка со ссылкой (страница сайта или кастомный URL)', icon: 'link');
    }
}
