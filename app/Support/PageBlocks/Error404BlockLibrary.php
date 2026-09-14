<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use App\Support\PageBlocks\Blocks\Error\Error404Block;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;

/**
 * Flexible-layouts block definitions for the error-404 setting
 * (settings.value, key=error-404).
 *
 * The 404 page is not a DB page — it renders through errors/404.blade.php
 * with HTTP 404. An empty setting value keeps the static prototype markup.
 */
class Error404BlockLibrary
{
    /**
     * Block set for the error-404 setting.
     *
     * An optional prebuilt field lets other libraries append their blocks
     * onto the same instance (see SettingFormPage's AJAX fallback).
     */
    public static function error404(?FlexibleLayouts $layouts = null): FlexibleLayouts
    {
        $layouts ??= FlexibleLayouts::make('Значение', 'value');

        Error404Block::register($layouts);

        return $layouts;
    }
}
