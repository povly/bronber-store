<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Loyalty;

use App\Support\PageBlocks\Blocks\PageBlock;
use MoonShine\UI\Fields\Text;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use Sckatik\MoonshineEditorJs\Fields\EditorJs;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * «loyalty-how-works» — «Как это работает?»: заголовок секции +
 * карточки-шаги (иконка, заголовок, описание-EditorJS).
 * Прототип: blocks/loyalty/how-works/how-works.blade.php;
 * разметку текста рендерит RenderEditorJs во вью блока.
 */
final class LoyaltyHowWorksBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('loyalty-how-works', 'Лояльность — как это работает', [
            Text::make('Заголовок', 'title')
                ->escapeOnApply(static fn (): bool => false),
            FlexibleLayouts::make('Шаги', 'items')
                ->block('item', 'Шаг', [
                    MediaManagerPicker::make('Иконка', 'icon')
                        ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'svg']),
                    Text::make('Заголовок', 'title')
                        ->escapeOnApply(static fn (): bool => false),
                    EditorJs::make('Описание (EditorJS)', 'text'),
                ]),
        ], limit: 1, category: 'Лояльность', description: 'Заголовок + шаги: иконка, заголовок, описание-EditorJS (прототип /loyalty)', icon: 'queue-list');
    }
}
