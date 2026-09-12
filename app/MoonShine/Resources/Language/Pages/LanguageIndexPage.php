<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Language\Pages;

use App\Models\Language;
use App\MoonShine\Resources\Language\LanguageResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<LanguageResource>
 */
final class LanguageIndexPage extends IndexPage
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

            Text::make('Код', 'code')
                ->sortable()
                ->badge(Color::PURPLE),

            Text::make('Название', 'name')->sortable(),

            Text::make(
                'По умолчанию',
                'is_default',
                formatted: static fn (Language $language): string => $language->is_default ? 'Да' : 'Нет',
            )->badge(static fn (bool $isDefault): Color => $isDefault ? Color::SUCCESS : Color::GRAY),

            Number::make('Сортировка', 'sort_order')->sortable(),
        ];
    }

    #[\Override]
    protected function modifyListComponent(ComponentContract $component): TableBuilder
    {
        return $component->columnSelection();
    }
}
