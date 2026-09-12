<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\PageTranslation\Pages;

use App\Models\Language;
use App\MoonShine\Resources\PageTranslation\PageTranslationResource;
use App\Services\Languages\LanguageService;
use App\Support\PageBlocks\PageBlockLibrary;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Validation\Rule;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * @extends FormPage<PageTranslationResource>
 */
final class PageTranslationFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    #[\Override]
    protected function fields(): iterable
    {
        return [
            ID::make(),

            Select::make('Язык', 'locale')
                ->options(
                    Language::query()->orderBy('sort_order')->pluck('name', 'code')->all()
                )
                ->required()
                ->hint('Перевод для выбранного языка; у страницы может быть только один перевод на язык')
                ->readonly(static fn (Select $field): bool => $field->getData()?->getOriginal()?->exists ?? false),

            Text::make('Заголовок', 'title')->required(),
            Text::make('Meta title', 'meta_title'),
            Textarea::make('Meta description', 'meta_description'),
            Textarea::make('Meta keywords', 'meta_keywords'),
            Text::make('Canonical URL', 'canonical_url'),
            MediaManagerPicker::make('OG-изображение', 'og_image')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp']),

            PageBlockLibrary::page(),
        ];
    }

    #[\Override]
    protected function rules(DataWrapperContract $item): array
    {
        $translation = $item->getOriginal();

        return [
            'title' => ['required', 'string', 'max:255'],
            'locale' => [
                'required',
                'string',
                Rule::in(app(LanguageService::class)->codes()),
                Rule::unique('page_translations', 'locale')
                    ->where(
                        static fn (QueryBuilder $query): QueryBuilder => $query->where(
                            'page_id',
                            (int) request()->input('page_id', 0),
                        ),
                    )
                    ->ignore($translation?->getKey()),
            ],
        ];
    }
}
