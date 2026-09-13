<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Home;

use App\Support\PageBlocks\Blocks\PageBlock;
use MoonShine\UI\Fields\Text;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;

/**
 * «home-categories» — динамическая сетка категорий главной страницы:
 * контент берётся из CatalogMock::homeCategories(), редактируется
 * только заголовок секции.
 */
final class HomeCategoriesBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('home-categories', 'Категории (динамический)', [
            Text::make('Заголовок секции', 'title')
                ->default('Категории')
                ->escapeOnApply(static fn (): bool => false),
        ], category: 'Главная', description: 'Сетка из 12 категорий каталога (сейчас mock, позже CRM-склад)', icon: 'squares-2x2');
    }
}
