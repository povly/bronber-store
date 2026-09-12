<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Page;

use App\Models\Page;
use App\MoonShine\Resources\Page\Pages\PageFormPage;
use App\MoonShine\Resources\Page\Pages\PageIndexPage;
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
 * @extends ModelResource<Page, PageIndexPage, PageFormPage, null>
 */
#[Icon('document-text')]
#[Group('content')]
#[Order(10)]
class PageResource extends ModelResource
{
    protected string $model = Page::class;

    protected string $column = 'slug';

    protected array $with = ['translations'];

    protected bool $simplePaginate = true;

    #[\Override]
    public function getTitle(): string
    {
        return __('Страницы');
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
            PageIndexPage::class,
            PageFormPage::class,
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
        /** @var Page $page */
        $page = $item->getOriginal();

        $codes = app(LanguageService::class)->codes();
        $existing = $page->translations()->pluck('locale')->all();

        foreach (array_diff($codes, $existing) as $locale) {
            $page->translations()->create([
                'locale' => $locale,
                'title' => $page->slug,
            ]);
        }

        Log::info('[PageResource] page_id={id} saved by moonshine_user_id={uid}', [
            'id' => $page->getKey(),
            'uid' => auth('moonshine')->id(),
        ]);

        return $item;
    }
}
