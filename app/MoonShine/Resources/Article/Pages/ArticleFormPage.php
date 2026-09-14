<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Article\Pages;

use App\MoonShine\Resources\Article\ArticleResource;
use App\MoonShine\Resources\ArticleTranslation\ArticleTranslationResource;
use App\Services\Languages\LanguageService;
use Illuminate\Validation\Rule;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * @extends FormPage<ArticleResource>
 */
final class ArticleFormPage extends FormPage
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
                Tab::make('Статья', [
                    Text::make('Slug', 'slug')
                        ->required()
                        ->hint('Часть URL статьи, например: zapusk-bronber-auto-service'),

                    Date::make('Дата публикации', 'published_at')
                        ->required()
                        ->hint('Показывается на карточке и в шапке статьи; сортировка блога — по убыванию даты'),

                    Switcher::make('Опубликована', 'is_published')->default(true),

                    MediaManagerPicker::make('Обложка (десктоп)', 'cover_pc')
                        ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp']),

                    MediaManagerPicker::make('Обложка (мобильная)', 'cover_mb')
                        ->hint('Пусто — используется десктопная обложка')
                        ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp']),
                ])->icon('newspaper'),

                Tab::make('Переводы', [
                    HasMany::make('Переводы', 'translations', resource: ArticleTranslationResource::class)
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
                Rule::unique('articles', 'slug')->ignore($model?->getKey()),
            ],
            'published_at' => ['required', 'date'],
            'translations.*.locale' => [Rule::in(resolve(LanguageService::class)->codes())],
            'translations.*.title' => ['required', 'string', 'max:255'],
        ];
    }
}
