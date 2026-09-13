<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Setting\Pages;

use App\Models\Setting;
use App\MoonShine\Resources\Setting\SettingResource;
use App\Support\PageBlocks\FooterBlockLibrary;
use App\Support\PageBlocks\HeaderBlockLibrary;
use App\Support\PageBlocks\MobileMenuBlockLibrary;
use App\Support\PageBlocks\MobileNavBlockLibrary;
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
            'header' => HeaderBlockLibrary::header(),
            'mobile-menu' => MobileMenuBlockLibrary::mobileMenu(),
            'mobile-nav' => MobileNavBlockLibrary::mobileNav(),
            // The flexible-layouts AJAX store route carries no resourceItem,
            // so the setting is unresolvable there and getItem() is null —
            // serve the union of all setting blocks so picker adds work for
            // every setting key (the browser picker only offers the blocks
            // of the rendered form, so extra blocks are never clickable).
            default => MobileNavBlockLibrary::mobileNav(
                MobileMenuBlockLibrary::mobileMenu(
                    FooterBlockLibrary::footer(HeaderBlockLibrary::header()),
                ),
            ),
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
