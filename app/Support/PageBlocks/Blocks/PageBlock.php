<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks;

use Povly\FlexibleLayouts\Fields\FlexibleLayouts;

/**
 * A single flexible-layouts block definition.
 *
 * One class per block type — block sets never grow into a single
 * god-method. Implementations register themselves on the shared
 * FlexibleLayouts field via {@see self::register()}.
 *
 * Naming convention: block types are page-prefixed and mirror the
 * prototype partials — «home-hero» ↔ blocks/home/hero.blade.php.
 * Blocks of other prototype pages follow the same rule later
 * («category-*», «contact-*», …).
 */
interface PageBlock
{
    public static function register(FlexibleLayouts $layouts): void;
}
