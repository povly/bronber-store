<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;
use MoonShine\ColorManager\ColorManager;
use MoonShine\ColorManager\Palettes\PurplePalette;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\UI\Components\FlexibleRender;
use Povly\MoonShineImageEditor\Support\ImageEditorRenderer;
use YuriZoom\MoonShineMediaManager\Components\MediaManagerOffCanvas;

final class MoonShineLayout extends AppLayout
{
    /**
     * @var null|class-string<PaletteContract>
     */
    protected ?string $palette = PurplePalette::class;

    #[\Override]
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            Css::make('/vendor/image-editor/image-editor.css'),
            Js::make('/vendor/image-editor/filerobot-image-editor.min.js'),
            Js::make('/vendor/image-editor/image-editor.js'),
        ];
    }

    #[\Override]
    protected function menu(): array
    {
        return [
            ...parent::menu(),
        ];
    }

    /**
     * @param  ColorManager  $colorManager
     */
    #[\Override]
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#00000');
    }

    #[\Override]
    protected function getContentComponents(): array
    {
        return [
            ...parent::getContentComponents(),
            MediaManagerOffCanvas::make(),
            FlexibleRender::make(
                resolve(ImageEditorRenderer::class)->renderModal(),
            ),
        ];
    }
}
