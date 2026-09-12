<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Language\Pages;

use App\MoonShine\Resources\Language\LanguageResource;
use Illuminate\Validation\Rule;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<LanguageResource>
 */
final class LanguageFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    #[\Override]
    protected function fields(): iterable
    {
        return [
            ID::make(),

            Text::make('Код', 'code')
                ->required()
                ->hint('Двухбуквенный код, например: de. Ключ связей — после создания не меняется.')
                ->readonly(static fn (Text $field): bool => $field->getData()?->getOriginal()?->exists ?? false),

            Text::make('Название', 'name')->required(),

            Number::make('Сортировка', 'sort_order')->min(0)->default(0),
            Switcher::make('По умолчанию', 'is_default')
                ->hint('Язык без префикса в URL и локаль-fallback'),
        ];
    }

    #[\Override]
    protected function rules(DataWrapperContract $item): array
    {
        $language = $item->getOriginal();

        return [
            'code' => [
                'required',
                'string',
                'max:12',
                'regex:/^[a-z]{2}(?:[-_][a-z]{2})?$/i',
                Rule::unique('languages', 'code')->ignore($language?->getKey()),
            ],
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
