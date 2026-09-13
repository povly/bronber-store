<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Page\Pages;

use App\Models\Page;
use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\PageTranslation\PageTranslationResource;
use App\Services\Languages\LanguageService;
use App\Support\PageBlocks\PageOptions;
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
use MoonShine\UI\Fields\Select;
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
        /** @var Page|null $page */
        $page = $this->getResource()->getItem();

        // Parent candidates: published pages except the page itself (its
        // descendants are rejected by the cycle rule below).
        $parentOptions = PageOptions::published();
        unset($parentOptions[$page?->slug ?? '']);

        return [
            ID::make(),

            Tabs::make([
                Tab::make('Страница', [
                    Text::make('Slug', 'slug')
                        ->required()
                        ->hint('Часть URL, например: o-kompanii'),

                    Select::make('Родительская страница', 'parent_id')
                        ->options($parentOptions)
                        ->nullable()
                        ->hint('Строит хлебные крошки: Главная → родитель → эта страница'),

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
            'parent_id' => [
                'nullable',
                Rule::exists('pages', 'id')->whereNot('id', $model?->getKey()),
                // Cycle guard: walk UP from the chosen parent — if this page
                // appears in its chain, the parent is our descendant.
                static function (string $attribute, mixed $value, \Closure $fail) use ($model): void {
                    if ($value === null || $model === null || ! $model->exists) {
                        return;
                    }

                    $cursor = Page::query()->find($value);

                    for ($depth = 0; $depth < 10 && $cursor !== null; $depth++) {
                        if ($cursor->getKey() === $model->getKey()) {
                            $fail('Выбранный родитель является потомком этой страницы (цикл).');

                            return;
                        }

                        $cursor = $cursor->parent_id === null ? null : $cursor->parent()->first();
                    }
                },
            ],
            'translations.*.locale' => [Rule::in(resolve(LanguageService::class)->codes())],
            'translations.*.title' => ['required', 'string', 'max:255'],
        ];
    }
}
