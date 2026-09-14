<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Setting\Pages;

use App\Models\Setting;
use App\MoonShine\Resources\Setting\SettingResource;
use App\Support\PageBlocks\Error404BlockLibrary;
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
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;

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
            'error-404' => Error404BlockLibrary::error404(),
            // The flexible-layouts AJAX store route carries no resourceItem,
            // so the setting is unresolvable there and getItem() is null —
            // serve the union of all setting blocks so picker adds work for
            // every setting key (the browser picker only offers the blocks
            // of the rendered form, so extra blocks are never clickable).
            //
            // Union is merged first-wins: library names collide (logo exists
            // in header/footer/mobile-menu, contacts in footer/mobile-menu)
            // and FlexibleLayouts::block() rejects duplicates within one
            // field. First-wins preserves the previous first-match lookup.
            default => self::unionField(),
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

    /**
     * AJAX fallback union of all setting block libraries, merged first-wins.
     *
     * @see self::fields() default branch for the reason this exists.
     */
    private static function unionField(): FlexibleLayouts
    {
        $union = FlexibleLayouts::make('Значение', 'value');

        $libraries = [
            HeaderBlockLibrary::header(),
            FooterBlockLibrary::footer(),
            MobileMenuBlockLibrary::mobileMenu(),
            MobileNavBlockLibrary::mobileNav(),
            Error404BlockLibrary::error404(),
        ];

        foreach ($libraries as $library) {
            foreach ($library->blocks() as $block) {
                if (! is_null($union->blocks()->findByName($block->name()))) {
                    continue;
                }

                $union->block(
                    $block->name(),
                    $block->title(),
                    $block->fields(),
                    $block->limit(),
                    $block->category(),
                    $block->description(),
                    $block->icon(),
                );
            }
        }

        return $union;
    }
}
