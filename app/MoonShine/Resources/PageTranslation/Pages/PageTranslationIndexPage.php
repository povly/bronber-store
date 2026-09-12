<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\PageTranslation\Pages;

use App\MoonShine\Resources\PageTranslation\PageTranslationResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<PageTranslationResource>
 */
final class PageTranslationIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<FieldContract>
     */
    #[\Override]
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Язык', 'locale')->badge(Color::PURPLE),
            Text::make('Заголовок', 'title'),
        ];
    }

    #[\Override]
    protected function modifyListComponent(ComponentContract $component): TableBuilder
    {
        return $component->columnSelection();
    }
}
