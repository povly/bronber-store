<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Language;

use App\Models\Language;
use App\MoonShine\Resources\Language\Pages\LanguageFormPage;
use App\MoonShine\Resources\Language\Pages\LanguageIndexPage;
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
 * @extends ModelResource<Language, LanguageIndexPage, LanguageFormPage, null>
 */
#[Icon('globe-alt')]
#[Group('settings')]
#[Order(20)]
class LanguageResource extends ModelResource
{
    protected string $model = Language::class;

    protected string $column = 'name';

    protected bool $simplePaginate = true;

    #[\Override]
    public function getTitle(): string
    {
        return __('Языки');
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
            LanguageIndexPage::class,
            LanguageFormPage::class,
        ];
    }

    #[\Override]
    protected function search(): array
    {
        return ['code', 'name'];
    }

    /**
     * The system must always keep exactly one default language:
     * setting a new default demotes all others, unsetting the last
     * default is silently reverted.
     */
    #[\Override]
    protected function afterSave(DataWrapperContract $item, FieldsContract $fields): DataWrapperContract
    {
        /** @var Language $language */
        $language = $item->getOriginal();

        if ($language->is_default) {
            Language::query()
                ->where('is_default', true)
                ->whereKeyNot($language->getKey())
                ->update(['is_default' => false]);
        } elseif (! Language::query()
            ->where('is_default', true)
            ->whereKeyNot($language->getKey())
            ->exists()
        ) {
            $language->is_default = true;
            $language->save();
        }

        Log::info('[LanguageResource] language code={code} saved, default_changed={bool}', [
            'code' => $language->code,
            'default_changed' => $language->wasChanged('is_default'),
        ]);

        return $item;
    }
}
