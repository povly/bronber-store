<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Page\Pages;

use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\PageTranslation\PageTranslationResource;
use App\Services\Languages\LanguageService;
use Illuminate\Validation\Rule;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<PageResource>
 */
final class PageFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    #[\Override]
    protected function fields(): iterable
    {
        return [
            ID::make(),

            Tabs::make([
                Tab::make('Страница', [
                    Text::make('Slug', 'slug')
                        ->required()
                        ->hint('Часть URL, например: o-kompanii'),

                    Number::make('Сортировка', 'sort_order')->min(0)->default(0),

                    Switcher::make('Опубликована', 'is_published')->default(true),
                ])->icon('document-text'),

                Tab::make('Переводы', [
                    HasMany::make('Переводы', 'translations', resource: PageTranslationResource::class)
                        ->creatable(),
                ])->icon('language'),
            ]),
        ];
    }

    #[\Override]
    protected function rules(DataWrapperContract $item): array
    {
        $model = $item->getOriginal();

        return [
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('pages', 'slug')->ignore($model?->getKey()),
            ],
            'sort_order' => ['integer', 'min:0'],
            'translations.*.locale' => [Rule::in(resolve(LanguageService::class)->codes())],
            'translations.*.title' => ['required', 'string', 'max:255'],
        ];
    }
}
