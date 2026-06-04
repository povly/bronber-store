<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\ColorManager\Palettes\PurplePalette;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use MoonShine\UI\Components\FlexibleRender;
use Povly\MoonShineImageEditor\Support\ImageEditorRenderer;
use YuriZoom\MoonShineMediaManager\Components\MediaManagerOffCanvas;

final class MoonShineLayout extends AppLayout
{
    /**
     * @var null|class-string<PaletteContract>
     */
    protected ?string $palette = PurplePalette::class;

    protected function assets(): array
    {
        return [
            ...parent::assets(),
            Css::make('/vendor/image-editor/image-editor.css'),
            Js::make('/vendor/image-editor/filerobot-image-editor.min.js'),
            Js::make('/vendor/image-editor/image-editor.js'),
        ];
    }

    protected function menu(): array
    {
        return [
            ...parent::menu(),
        ];
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#00000');
    }

    protected function getContentComponents(): array
    {
        return [
            ...parent::getContentComponents(),
            MediaManagerOffCanvas::make(),
            FlexibleRender::make(
                app(ImageEditorRenderer::class)->renderModal(),
            ),
        ];
    }
}
