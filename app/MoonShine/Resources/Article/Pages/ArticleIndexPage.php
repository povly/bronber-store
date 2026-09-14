<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Article\Pages;

use App\Models\Article;
use App\MoonShine\Resources\Article\ArticleResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<ArticleResource>
 */
final class ArticleIndexPage extends IndexPage
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
                formatted: static fn (Article $article): string => $article->translation()?->title ?? '—',
            ),

            Text::make(
                'Статус',
                'is_published',
                formatted: static fn (Article $article): string => $article->is_published ? 'Опубликована' : 'Черновик',
            )->badge(static fn (bool $isPublished): Color => $isPublished ? Color::SUCCESS : Color::GRAY),

            Date::make('Дата публикации', 'published_at')->sortable(),
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
