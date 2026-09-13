<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Returns;

use App\Support\PageBlocks\Blocks\PageBlock;
use MoonShine\UI\Fields\Text;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use Sckatik\MoonshineEditorJs\Fields\EditorJs;

/**
 * «returns-content» — контент страницы «Возврат и обмен» (/returns,
 * прототип: blocks/returns/returns.blade.php): заголовок h1 + весь
 * текст страницы одним EditorJS-документом (абзацы, подзаголовки,
 * списки). Разметку рендерит RenderEditorJs во вью блока.
 */
final class ReturnsContentBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('returns-content', 'Возврат — контент (EditorJS)', [
            Text::make('Заголовок', 'title')
                ->escapeOnApply(static fn (): bool => false),
            EditorJs::make('Контент (EditorJS)', 'body')
                ->escapeOnApply(static fn (): bool => false),
        ], limit: 1, category: 'Возврат', description: 'Заголовок h1 + весь текст страницы одним EditorJS-документом (прототип /returns)', icon: 'receipt-refund');
    }
}
