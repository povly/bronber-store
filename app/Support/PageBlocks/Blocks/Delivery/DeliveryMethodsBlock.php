<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Delivery;

use App\Support\PageBlocks\Blocks\PageBlock;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * «delivery-methods» — заголовок + карточки способов оплаты и доставки
 * (прототип: blocks/delivery/delivery.blade.php).
 */
final class DeliveryMethodsBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('delivery-methods', 'Доставка — способы оплаты и доставки', [
            Text::make('Заголовок', 'title')
                ->escapeOnApply(static fn (): bool => false),
            FlexibleLayouts::make('Карточки', 'items')
                ->block('item', 'Карточка', [
                    Text::make('Заголовок', 'title')
                        ->escapeOnApply(static fn (): bool => false),
                    MediaManagerPicker::make('Иконка', 'icon')
                        ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'svg']),
                    Textarea::make('Описание', 'text')
                        ->escapeOnApply(static fn (): bool => false),
                ]),
        ], limit: 1, category: 'Доставка', description: 'Заголовок + карточки: иконка, заголовок, описание (прототип /delivery)', icon: 'credit-card');
    }
}
