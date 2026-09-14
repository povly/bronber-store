<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Error;

use App\Support\PageBlocks\Blocks\PageBlock;
use App\Support\PageBlocks\Concerns\BuildsLinkFields;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;

/**
 * «error-404» — страница ошибки 404: заголовок, описание и кнопки
 * (текст + двухтиповая ссылка + вариант оформления). Код «404» —
 * статическая часть вёрстки, не контент. Рендерится не PageController,
 * а errors/404.blade.php из настроек (ключ error-404); пустое значение
 * настройки оставляет статический прототип.
 * Прототип: blocks/error-404/error-404.blade.php.
 */
final class Error404Block implements PageBlock
{
    use BuildsLinkFields;

    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('error-404', 'Ошибка 404', [
            Text::make('Заголовок', 'title')
                ->escapeOnApply(static fn (): bool => false),
            Textarea::make('Описание', 'text')
                ->hint('Допускается HTML: <br>')
                ->escapeOnApply(static fn (): bool => false),
            Json::make('Кнопки', 'buttons')
                ->fields([
                    ...self::linkFields(),
                    Select::make('Тип кнопки', 'variant')
                        ->options([
                            'primary' => 'Основная (фиолетовая)',
                            'white-border' => 'С белой обводкой',
                        ])
                        ->default('primary'),
                ]),
        ], limit: 1, category: 'Служебное', description: 'Страница «не найдено»: заголовок, описание и кнопки-ссылки (пусто — статика прототипа)', icon: 'x-circle');
    }
}
