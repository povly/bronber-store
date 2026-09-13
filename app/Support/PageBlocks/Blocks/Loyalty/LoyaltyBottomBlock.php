<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Loyalty;

use App\Support\PageBlocks\Blocks\PageBlock;
use MoonShine\UI\Fields\Color;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * «loyalty-bottom» — нижняя секция в две колонки: слева — заголовок,
 * строки-примеры (левый/правый текст + цвет выделения) и баннер-подарок
 * (иконка, заголовок, текст); справа — заголовок и FAQ-аккордеон.
 * Прототип: blocks/loyalty/bottom/bottom.blade.php.
 */
final class LoyaltyBottomBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('loyalty-bottom', 'Лояльность — пример и FAQ', [
            Text::make('Заголовок (левая колонка)', 'title')
                ->escapeOnApply(static fn (): bool => false),
            FlexibleLayouts::make('Строки примера', 'rows')
                ->block('row', 'Строка', [
                    Text::make('Текст слева', 'left_text')
                        ->escapeOnApply(static fn (): bool => false),
                    Text::make('Текст справа', 'right_text')
                        ->escapeOnApply(static fn (): bool => false),
                    Color::make('Цвет', 'color')
                        ->hint('Пусто — обычная строка; цвет окрашивает оба текста (замена --accent прототипа)'),
                ]),
            MediaManagerPicker::make('Иконка подарка', 'gift_icon')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'svg']),
            Text::make('Заголовок баннера', 'gift_title')
                ->escapeOnApply(static fn (): bool => false),
            Textarea::make('Текст баннера', 'gift_text')
                ->escapeOnApply(static fn (): bool => false),
            Text::make('Заголовок FAQ (правая колонка)', 'faq_title')
                ->escapeOnApply(static fn (): bool => false),
            FlexibleLayouts::make('Вопросы', 'faqs')
                ->block('item', 'Вопрос', [
                    Text::make('Вопрос', 'question')
                        ->escapeOnApply(static fn (): bool => false),
                    Textarea::make('Ответ', 'answer')
                        ->escapeOnApply(static fn (): bool => false),
                ]),
        ], limit: 1, category: 'Лояльность', description: 'Пример начисления (строки + подарок) и FAQ-аккордеон (прототип /loyalty)', icon: 'gift');
    }
}
