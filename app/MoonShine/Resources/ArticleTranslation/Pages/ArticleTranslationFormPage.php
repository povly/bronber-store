<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ArticleTranslation\Pages;

use App\Models\Language;
use App\MoonShine\Resources\ArticleTranslation\ArticleTranslationResource;
use App\Services\Languages\LanguageService;
use App\Support\PageBlocks\ArticleBlockLibrary;
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
 * @extends FormPage<ArticleTranslationResource>
 */
final class ArticleTranslationFormPage extends FormPage
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
                ->hint('Перевод для выбранного языка; у статьи может быть только один перевод на язык')
                ->readonly(static fn (Select $select): bool => $select->getData()?->getOriginal()?->exists ?? false),

            Text::make('Заголовок', 'title')
                ->required()
                ->escapeOnApply(static fn (): bool => false),
            Text::make('Тег', 'tag')
                ->hint('Метка на карточке статьи, например: #запуск')
                ->escapeOnApply(static fn (): bool => false),
            Textarea::make('Краткое описание', 'excerpt')
                ->hint('Показывается на карточке статьи в листинге блога')
                ->escapeOnApply(static fn (): bool => false),

            Text::make('Meta title', 'meta_title')
                ->escapeOnApply(static fn (): bool => false),
            Textarea::make('Meta description', 'meta_description')
                ->escapeOnApply(static fn (): bool => false),
            Textarea::make('Meta keywords', 'meta_keywords')
                ->escapeOnApply(static fn (): bool => false),
            Text::make('Canonical URL', 'canonical_url')
                ->escapeOnApply(static fn (): bool => false),
            MediaManagerPicker::make('OG-изображение', 'og_image')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp']),

            ArticleBlockLibrary::article(),
        ];
    }

    #[\Override]
    protected function rules(DataWrapperContract $item): array
    {
        $model = $item->getOriginal();

        return [
            'title' => ['required', 'string', 'max:255'],
            'tag' => ['nullable', 'string', 'max:255'],
            'locale' => [
                'required',
                'string',
                Rule::in(resolve(LanguageService::class)->codes()),
                Rule::unique('article_translations', 'locale')
                    ->where(
                        static fn (QueryBuilder $queryBuilder): QueryBuilder => $queryBuilder->where(
                            'article_id',
                            (int) request()->input('article_id', 0),
                        ),
                    )
                    ->ignore($model?->getKey()),
            ],
        ];
    }
}
