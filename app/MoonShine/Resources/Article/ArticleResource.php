<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Article;

use App\Models\Article;
use App\MoonShine\Resources\Article\Pages\ArticleFormPage;
use App\MoonShine\Resources\Article\Pages\ArticleIndexPage;
use App\Services\Languages\LanguageService;
use Illuminate\Support\Facades\Log;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<Article, ArticleIndexPage, ArticleFormPage, null>
 */
#[Icon('newspaper')]
#[Group('content')]
#[Order(15)]
class ArticleResource extends ModelResource
{
    protected string $model = Article::class;

    protected string $column = 'slug';

    protected array $with = ['translations'];

    protected bool $simplePaginate = true;

    #[\Override]
    public function getTitle(): string
    {
        return __('Статьи');
    }

    #[\Override]
    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    #[\Override]
    protected function pages(): array
    {
        return [
            ArticleIndexPage::class,
            ArticleFormPage::class,
        ];
    }

    #[\Override]
    protected function search(): array
    {
        return ['slug'];
    }

    /**
     * Ensure a translation row exists for every active language after saving,
     * so each language gets its own tab with content in the form.
     */
    #[\Override]
    protected function afterSave(DataWrapperContract $item, FieldsContract $fields): DataWrapperContract
    {
        /** @var Article $article */
        $article = $item->getOriginal();

        $codes = resolve(LanguageService::class)->codes();
        $existing = $article->translations()->pluck('locale')->all();

        foreach (array_diff($codes, $existing) as $locale) {
            $article->translations()->create([
                'locale' => $locale,
                'title' => $article->slug,
            ]);
        }

        Log::info('[ArticleResource] article_id={id} saved by moonshine_user_id={uid}', [
            'id' => $article->getKey(),
            'uid' => auth('moonshine')->id(),
        ]);

        return $item;
    }
}
