<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Setting\Pages;

use App\Models\Setting;
use App\MoonShine\Resources\Setting\SettingResource;
use App\Support\PageBlocks\FooterBlockLibrary;
use App\Support\PageBlocks\HeaderBlockLibrary;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<SettingResource>
 */
final class SettingFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    #[\Override]
    protected function fields(): iterable
    {
        /** @var Setting|null $setting */
        $setting = $this->getResource()->getItem();

        $value = match ($setting?->key) {
            'footer' => FooterBlockLibrary::footer(),
            default => HeaderBlockLibrary::header(),
        };

        return [
            ID::make(),

            Text::make('Ключ', 'key')
                ->readonly()
                ->canApply(static fn (): bool => false),

            Text::make('Язык', 'locale')
                ->readonly()
                ->canApply(static fn (): bool => false),

            $value,
        ];
    }

    #[\Override]
    protected function rules(DataWrapperContract $item): array
    {
        return [];
    }
}
