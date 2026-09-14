<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Article;

use App\Support\PageBlocks\Blocks\PageBlock;
use MoonShine\UI\Fields\Text;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;

/**
 * «article-related» — секция «Другие новости» в конце статьи:
 * редактируется только заголовок, список статей динамический —
 * его передаёт BlogService (паттерн home-categories).
 */
final class ArticleRelatedBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('article-related', 'Статья — другие новости (динамический)', [
            Text::make('Заголовок секции', 'title')
                ->default('Другие новости')
                ->escapeOnApply(static fn (): bool => false),
        ], limit: 1, category: 'Статья', description: 'Карточки свежих статей (тянутся из блога автоматически)', icon: 'queue-list');
    }
}
