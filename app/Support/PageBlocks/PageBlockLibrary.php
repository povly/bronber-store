<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use App\Support\PageBlocks\Blocks\Faq\FaqItemsBlock;
use App\Support\PageBlocks\Blocks\Home\HomeAdvsBlock;
use App\Support\PageBlocks\Blocks\Home\HomeCategoriesBlock;
use App\Support\PageBlocks\Blocks\Home\HomeHeroBlock;
use App\Support\PageBlocks\Blocks\Home\HomeNewsBlock;
use App\Support\PageBlocks\Blocks\Home\HomePartnersBlock;
use App\Support\PageBlocks\Blocks\Home\HomeProductsBlock;
use App\Support\PageBlocks\Blocks\PageBlock;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;

/**
 * Flexible-layouts block definitions for page content
 * (page_translations.content).
 *
 * Header and footer block sets live in their own libraries
 * ({@see HeaderBlockLibrary}, {@see FooterBlockLibrary}) — SOLID split by
 * context instead of one god-class. Page content blocks are split the
 * same way: one class per block type under Blocks/, aggregated here.
 *
 * The picker offers page-prefixed prototype blocks only («home-*» ↔
 * blocks/home/*, «faq-*» ↔ blocks/faq/*; other pages will follow with
 * «category-*», «contact-*», …). Legacy generic types (hero, text, gallery, faq,
 * contacts, featured-products) are no longer registered here, but
 * their views stay — BlockRenderer still renders them for already
 * stored/demo content.
 */
class PageBlockLibrary
{
    /**
     * Block classes registered on the page content field.
     *
     * @return list<class-string<PageBlock>>
     */
    private static function blocks(): array
    {
        return [
            HomeHeroBlock::class,
            HomeCategoriesBlock::class,
            HomeAdvsBlock::class,
            HomeProductsBlock::class,
            HomePartnersBlock::class,
            HomeNewsBlock::class,
            FaqItemsBlock::class,
        ];
    }

    /**
     * Block set for the page content field (page_translations.content).
     */
    public static function page(): FlexibleLayouts
    {
        $layouts = FlexibleLayouts::make('Контент', 'content');

        foreach (self::blocks() as $block) {
            $block::register($layouts);
        }

        return $layouts;
    }
}
