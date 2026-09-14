<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use App\Support\PageBlocks\Blocks\Article\ArticleContentBlock;
use App\Support\PageBlocks\Blocks\Article\ArticleCtaBlock;
use App\Support\PageBlocks\Blocks\Article\ArticleGalleryBlock;
use App\Support\PageBlocks\Blocks\Article\ArticleRelatedBlock;
use App\Support\PageBlocks\Blocks\PageBlock;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;

/**
 * Flexible-layouts block definitions for article content
 * (article_translations.content).
 *
 * Mirrors {@see PageBlockLibrary}, but for the blog entity: block
 * types are article-prefixed («article-*» ↔ blocks/article/*),
 * one class per block type under Blocks/Article/, aggregated here.
 */
class ArticleBlockLibrary
{
    /**
     * Block classes registered on the article content field.
     *
     * @return list<class-string<PageBlock>>
     */
    private static function blocks(): array
    {
        return [
            ArticleContentBlock::class,
            ArticleGalleryBlock::class,
            ArticleCtaBlock::class,
            ArticleRelatedBlock::class,
        ];
    }

    /**
     * Block set for the article content field (article_translations.content).
     */
    public static function article(): FlexibleLayouts
    {
        $layouts = FlexibleLayouts::make('Контент', 'content');

        foreach (self::blocks() as $block) {
            $block::register($layouts);
        }

        return $layouts;
    }

    /**
     * Media slots per article block type for the locale media fallback
     * ({@see MediaFallback}): '<list>' => [keys] declares item-level
     * media keys. Keep in sync with the MediaManagerPicker fields of
     * the block classes.
     *
     * @return array<string, array<string|int, mixed>>
     */
    public static function mediaSchemas(): array
    {
        return [
            'article-gallery' => ['items' => ['image']],
        ];
    }
}
