<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Contact;

use App\Support\PageBlocks\Blocks\PageBlock;
use MoonShine\UI\Fields\Text;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * «contact-list» — заголовок + список контактов: иконка, текст, ссылка
 * (tel:/mailto:/URL). Прототип — контактная часть
 * blocks/delivery/delivery.blade.php; блок переиспользуем для будущей
 * страницы контактов.
 */
final class ContactListBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('contact-list', 'Контакты — список', [
            Text::make('Заголовок', 'title'),
            FlexibleLayouts::make('Контакты', 'items')
                ->block('item', 'Контакт', [
                    Text::make('Текст', 'text'),
                    Text::make('Ссылка', 'href')
                        ->hint('Готовый протокол: tel:, mailto: или https://; пусто — без ссылки'),
                    MediaManagerPicker::make('Иконка', 'icon')
                        ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'svg']),
                ]),
        ], limit: 1, category: 'Доставка', description: 'Заголовок + контакты: иконка, текст, ссылка (tel:/mailto:/URL)', icon: 'phone');
    }
}
