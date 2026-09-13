<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Faq;

use App\Support\PageBlocks\Blocks\PageBlock;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;

/**
 * «faq-items» — аккордеон вопрос-ответ страницы FAQ
 * (прототип: blocks/faq/faq.blade.php).
 */
final class FaqItemsBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('faq-items', 'FAQ — вопросы и ответы', [
            Text::make('Заголовок', 'title'),
            FlexibleLayouts::make('Вопросы', 'items')
                ->block('item', 'Вопрос', [
                    Text::make('Вопрос', 'question'),
                    Textarea::make('Ответ', 'answer'),
                ]),
        ], limit: 1, category: 'FAQ', description: 'Аккордеон вопрос-ответ с заголовком (прототип /faq)', icon: 'question-mark-circle');
    }
}
