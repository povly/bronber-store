<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Page\Pages;

use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\PageTranslation\PageTranslationResource;
use App\Services\Languages\LanguageService;
use App\Support\PageBlocks\PageBlockLibrary;
use Illuminate\Validation\Rule;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

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
                    Flex::make([
                        Column::make([
                            Text::make('Slug', 'slug')
                                ->required()
                                ->hint('Часть URL, например: o-kompanii'),
                        ])->columnSpan(6),

                        Column::make([
                            Flex::make([
                                Number::make('Сортировка', 'sort_order')->min(0)->default(0),
                                Switcher::make('Опубликована', 'is_published')->default(true),
                            ]),
                        ])->columnSpan(6),
                    ]),
                ])->icon('document-text'),

                Tab::make('Переводы', [
                    HasMany::make('Переводы', 'translations', resource: PageTranslationResource::class)
                        ->fields([
                            Text::make('Язык', 'locale')->readonly(),

                            Text::make('Заголовок', 'title')->required(),

                            Collapse::make('SEO', [
                                Text::make('Meta title', 'meta_title'),
                                Textarea::make('Meta description', 'meta_description'),
                                Textarea::make('Meta keywords', 'meta_keywords')
                                    ->hint('Через запятую'),
                                Select::make('Robots', 'meta_robots')->options([
                                    'index, follow' => 'Index, Follow (по умолчанию)',
                                    'noindex, follow' => 'Noindex, Follow',
                                    'index, nofollow' => 'Index, Nofollow',
                                    'noindex, nofollow' => 'Noindex, Nofollow',
                                ]),
                                Text::make('Canonical URL', 'canonical_url')
                                    ->hint('Оставьте пустым — каноническим станет текущий URL'),
                                MediaManagerPicker::make('OG-изображение', 'og_image')
                                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp'])
                                    ->hint('Для соцсетей (Open Graph / Twitter), 1200×630'),
                            ]),

                            PageBlockLibrary::page(),
                        ])
                        ->tabMode(),
                ])->icon('language'),
            ]),
        ];
    }

    #[\Override]
    protected function rules(DataWrapperContract $item): array
    {
        $page = $item->getOriginal();

        return [
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('pages', 'slug')->ignore($page?->getKey()),
            ],
            'sort_order' => ['integer', 'min:0'],
            'translations.*.locale' => [Rule::in(app(LanguageService::class)->codes())],
            'translations.*.title' => ['required', 'string', 'max:255'],
        ];
    }
}
