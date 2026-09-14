<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Loyalty;

use App\Support\PageBlocks\Blocks\PageBlock;
use MoonShine\UI\Fields\Text;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use Sckatik\MoonshineEditorJs\Fields\EditorJs;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * «loyalty-benefits» — полоса преимуществ: список «иконка + текст-
 * EditorJS»; пункт с заполненным заголовком становится «главным»
 * (большая иконка, класс модификатора --main у прототипа).
 * Прототип: blocks/loyalty/benefits/benefits.blade.php;
 * разметку текста рендерит RenderEditorJs во вью блока.
 */
final class LoyaltyBenefitsBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('loyalty-benefits', 'Лояльность — преимущества', [
            FlexibleLayouts::make('Пункты', 'items')
                ->block('item', 'Пункт', [
                    MediaManagerPicker::make('Иконка', 'icon')
                        ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'svg']),
                    Text::make('Заголовок', 'title')
                        ->hint('Заполнен — пункт становится «главным»: большая иконка и заголовок')
                        ->escapeOnApply(static fn (): bool => false),
                    EditorJs::make('Текст (EditorJS)', 'text'),
                ]),
        ], limit: 1, category: 'Лояльность', description: 'Список преимуществ: иконка, опциональный заголовок, текст-EditorJS (прототип /loyalty)', icon: 'star');
    }
}
