<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ArticleTranslation;

use App\Models\ArticleTranslation;
use App\MoonShine\Resources\ArticleTranslation\Pages\ArticleTranslationFormPage;
use App\MoonShine\Resources\ArticleTranslation\Pages\ArticleTranslationIndexPage;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * Technical resource backing the HasMany translations field of ArticleResource.
 * Not exposed in the admin menu — translations are edited from the article form.
 *
 * @extends ModelResource<ArticleTranslation, ArticleTranslationIndexPage, ArticleTranslationFormPage, null>
 */
class ArticleTranslationResource extends ModelResource
{
    protected string $model = ArticleTranslation::class;

    protected string $column = 'title';

    protected string $title = 'Переводы статей';

    #[\Override]
    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    /**
     * Bind a new translation to its parent article. The HasMany modal
     * submits a hidden article_id; direct requests may pass it in the
     * payload — both work.
     */
    #[\Override]
    protected function beforeCreating(DataWrapperContract $item): DataWrapperContract
    {
        $parentId = (int) request()->input('article_id', 0);

        if ($parentId > 0 && $item->getOriginal()->article_id === null) {
            $item->getOriginal()->article_id = $parentId;
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
            ArticleTranslationIndexPage::class,
            ArticleTranslationFormPage::class,
        ];
    }
}
