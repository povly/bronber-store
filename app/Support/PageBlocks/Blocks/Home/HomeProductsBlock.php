<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Home;

use App\Support\PageBlocks\Blocks\PageBlock;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;

/**
 * «home-products» — динамический слайдер товаров главной страницы:
 * данные из CatalogMock::featured(count), редактируются заголовок
 * и количество карточек.
 */
final class HomeProductsBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('home-products', 'Товары слайдером (динамический)', [
            Text::make('Заголовок секции', 'title'),
            Number::make('Количество товаров', 'count')
                ->min(1)
                ->max(12)
                ->default(4),
        ], category: 'Главная', description: 'Слайдер карточек товаров из каталога (сейчас mock, позже CRM-склад)', icon: 'shopping-bag');
    }
}
