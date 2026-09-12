<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\PageTranslation;

use App\Models\PageTranslation;
use App\MoonShine\Resources\PageTranslation\Pages\PageTranslationFormPage;
use App\MoonShine\Resources\PageTranslation\Pages\PageTranslationIndexPage;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * Technical resource backing the HasMany translations field of PageResource.
 * Not exposed in the admin menu — translations are edited from the page form.
 *
 * @extends ModelResource<PageTranslation, PageTranslationIndexPage, PageTranslationFormPage, null>
 */
class PageTranslationResource extends ModelResource
{
    protected string $model = PageTranslation::class;

    protected string $column = 'title';

    protected string $title = 'Переводы страниц';

    #[\Override]
    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    /**
     * Bind a new translation to its parent page. The HasMany modal submits
     * a hidden page_id; direct requests may pass it in the payload — both work.
     */
    #[\Override]
    protected function beforeCreating(DataWrapperContract $item): DataWrapperContract
    {
        $parentId = (int) request()->input('page_id', 0);

        if ($parentId > 0 && $item->getOriginal()->page_id === null) {
            $item->getOriginal()->page_id = $parentId;
        }

        return $item;
    }

    /**
     * @return list<class-string<PageContract>>
     */
    #[\Override]
    protected function pages(): array
    {
        return [
            PageTranslationIndexPage::class,
            PageTranslationFormPage::class,
        ];
    }
}
