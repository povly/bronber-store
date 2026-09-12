<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\PageTranslation\Pages;

use App\MoonShine\Resources\PageTranslation\PageTranslationResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Fields\ID;
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
            Text::make('Язык', 'locale')->readonly(),
            Text::make('Заголовок', 'title')->required(),
            Text::make('Meta title', 'meta_title'),
            Textarea::make('Meta description', 'meta_description'),
            Textarea::make('Meta keywords', 'meta_keywords'),
            Text::make('Canonical URL', 'canonical_url'),
            MediaManagerPicker::make('OG-изображение', 'og_image')
                ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp']),
        ];
    }

    #[\Override]
    protected function rules(DataWrapperContract $item): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
        ];
    }
}
