<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\About;

use App\Support\PageBlocks\Blocks\PageBlock;
use MoonShine\UI\Fields\Text;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use Sckatik\MoonshineEditorJs\Fields\EditorJs;

/**
 * «about-timeline» — история компании на странице «О компании»
 * (прототип: blocks/about/about.blade.php): заголовок + список
 * событий «дата + editorjs-текст» (слайдер лет и аккордеон).
 */
final class AboutTimelineBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('about-timeline', 'О компании — история (таймлайн)', [
            Text::make('Заголовок', 'title')
                ->escapeOnApply(static fn (): bool => false),
            FlexibleLayouts::make('События', 'items')
                ->block('item', 'Событие', [
                    Text::make('Заголовок (ПК)', 'title_desktop')
                        ->escapeOnApply(static fn (): bool => false),
                    Text::make('Заголовок (телефон)', 'title_mobile')
                        ->escapeOnApply(static fn (): bool => false),
                    EditorJs::make('Текст', 'text')
                        ->escapeOnApply(static fn (): bool => false),
                ]),
        ], limit: 1, category: 'О компании', description: 'Заголовок + список событий: дата и editorjs-текст (прототип /about)', icon: 'clock');
    }
}
