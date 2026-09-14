<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Setting\Pages;

use App\Models\Setting;
use App\MoonShine\Resources\Setting\SettingResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<SettingResource>
 */
final class SettingIndexPage extends IndexPage
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

            Text::make(
                'Ключ',
                'key',
                formatted: static fn (Setting $setting): string => match ($setting->key) {
                    'header' => 'Шапка',
                    'footer' => 'Подвал',
                    'mobile-menu' => 'Мобильное меню',
                    'mobile-nav' => 'Мобильная панель',
                    'error-404' => 'Ошибка 404',
                    default => $setting->key,
                },
            )->badge(Color::PURPLE),

            Text::make('Язык', 'locale')->badge(Color::GRAY),

            Text::make(
                'Блоков',
                'value',
                formatted: static fn (Setting $setting): string => (string) count($setting->value ?? []),
            ),
        ];
    }

    #[\Override]
    protected function filters(): iterable
    {
        return [
            Select::make('Ключ', 'key')->options([
                'header' => 'Шапка',
                'footer' => 'Подвал',
                'mobile-menu' => 'Мобильное меню',
                'mobile-nav' => 'Мобильная панель',
                'error-404' => 'Ошибка 404',
            ]),
        ];
    }

    #[\Override]
    protected function modifyListComponent(ComponentContract $component): TableBuilder
    {
        return $component->columnSelection();
    }
}
