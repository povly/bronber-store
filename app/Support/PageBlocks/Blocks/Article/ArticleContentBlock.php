<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Article;

use App\Support\PageBlocks\Blocks\PageBlock;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use Sckatik\MoonshineEditorJs\Fields\EditorJs;

/**
 * «article-content» — текст статьи одним EditorJS-документом
 * (абзацы, подзаголовки, списки). Разметку рендерит RenderEditorJs
 * во вью блока blocks/article/content.blade.php.
 *
 * Без limit: текст статьи можно чередовать с галереями
 * (article-gallery) в любом порядке.
 */
final class ArticleContentBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('article-content', 'Статья — текст (EditorJS)', [
            EditorJs::make('Текст статьи', 'text')
                ->escapeOnApply(static fn (): bool => false),
        ], category: 'Статья', description: 'Абзацы, подзаголовки и списки текста статьи (EditorJS)', icon: 'document-text');
    }
}
