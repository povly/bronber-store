<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Page\Pages;

use App\Models\Page;
use App\MoonShine\Resources\Page\PageResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<PageResource>
 */
final class PageIndexPage extends IndexPage
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

            Text::make('Slug', 'slug')->sortable(),

            Text::make(
                'Заголовок',
                formatted: static fn (Page $page): string => $page->translation()?->title ?? '—',
            ),

            Text::make(
                'Статус',
                'is_published',
                formatted: static fn (bool $isPublished): string => $isPublished ? 'Опубликована' : 'Черновик',
            )->badge(static fn (bool $isPublished): Color => $isPublished ? Color::SUCCESS : Color::GRAY),

            Number::make('Сортировка', 'sort_order')->sortable(),
        ];
    }

    /**
     * @param  TableBuilder  $component
     */
    #[\Override]
    protected function modifyListComponent(ComponentContract $component): TableBuilder
    {
        return $component->columnSelection();
    }
}
