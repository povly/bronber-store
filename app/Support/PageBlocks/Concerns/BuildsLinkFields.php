<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Concerns;

use App\Support\PageBlocks\PageOptions;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;

/**
 * Reusable MoonShine field set for two-type links
 * ({label, type: page|custom, page, url}) inside Json/flexible blocks.
 */
trait BuildsLinkFields
{
    /**
     * @return list<FieldContract>
     */
    protected static function linkFields(): array
    {
        return [
            Text::make('Название', 'label')
                ->required(),

            Select::make('Тип ссылки', 'type')
                ->options([
                    'page' => 'Страница сайта',
                    'custom' => 'Кастомная ссылка',
                ])
                ->default('page')
                ->required(),

            Select::make('Страница', 'page')
                ->options(static fn (): array => PageOptions::published())
                ->hint('Для типа «Страница сайта»; в списке только опубликованные страницы'),

            Text::make('URL', 'url')
                ->hint('Для типа «Кастомная ссылка»; относительные пути пишите с учётом локали'),
        ];
    }
}
