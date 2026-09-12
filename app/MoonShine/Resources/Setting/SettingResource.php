<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Setting;

use App\Models\Setting;
use App\MoonShine\Resources\Setting\Pages\SettingFormPage;
use App\MoonShine\Resources\Setting\Pages\SettingIndexPage;
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
 * @extends ModelResource<Setting, SettingIndexPage, SettingFormPage, null>
 */
#[Icon('cog-6-tooth')]
#[Group('settings')]
#[Order(30)]
class SettingResource extends ModelResource
{
    protected string $model = Setting::class;

    protected string $column = 'key';

    protected bool $simplePaginate = true;

    #[\Override]
    public function getTitle(): string
    {
        return __('Настройки');
    }

    #[\Override]
    protected function activeActions(): ListOf
    {
        // Settings rows are seeded (key × locale) — only editing is needed.
        return parent::activeActions()->except(Action::VIEW, Action::CREATE);
    }

    #[\Override]
    protected function pages(): array
    {
        return [
            SettingIndexPage::class,
            SettingFormPage::class,
        ];
    }

    #[\Override]
    protected function search(): array
    {
        return ['key'];
    }

    #[\Override]
    protected function afterSave(DataWrapperContract $item, FieldsContract $fields): DataWrapperContract
    {
        /** @var Setting $setting */
        $setting = $item->getOriginal();

        Log::info('[SettingResource] key={key} locale={locale} saved', [
            'key' => $setting->key,
            'locale' => $setting->locale,
        ]);

        return $item;
    }
}
