<?php

declare(strict_types=1);

namespace App\Support\PageBlocks\Blocks\Contacts;

use App\Support\PageBlocks\Blocks\PageBlock;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;

/**
 * «contacts-main» — главная секция страницы контактов: заголовок (h1),
 * описание, телефон, email, адрес, Яндекс-карта (iframe HTML) и
 * настройки формы заявок (прототип: blocks/contacts/contacts.blade.php).
 */
final class ContactsMainBlock implements PageBlock
{
    public static function register(FlexibleLayouts $layouts): void
    {
        $layouts->block('contacts-main', 'Контакты — шапка с формой и картой', [
            Text::make('Заголовок (h1)', 'title')
                ->hint('Можно использовать <br> для переносов')
                ->escapeOnApply(static fn (): bool => false),
            Textarea::make('Описание', 'subtitle')
                ->escapeOnApply(static fn (): bool => false),
            Text::make('Телефон', 'phone')
                ->hint('Отображается ссылкой tel:')
                ->escapeOnApply(static fn (): bool => false),
            Text::make('Email', 'email')
                ->hint('Отображается ссылкой mailto:')
                ->escapeOnApply(static fn (): bool => false),
            Text::make('Адрес', 'address')
                ->escapeOnApply(static fn (): bool => false),
            Textarea::make('Карта — HTML iframe (Яндекс)', 'map_html')
                ->hint('Вставьте <iframe> целиком из конструктора Яндекс.Карт')
                ->escapeOnApply(static fn (): bool => false),
            Text::make('Текст кнопки формы', 'submit_label')
                ->hint('Пусто — перевод по умолчанию')
                ->escapeOnApply(static fn (): bool => false),
            Textarea::make('Текст согласия', 'consent_text')
                ->hint('Пусто — перевод по умолчанию')
                ->escapeOnApply(static fn (): bool => false),
            Text::make('Сообщение об успешной отправке', 'success_message')
                ->hint('Пусто — перевод по умолчанию')
                ->escapeOnApply(static fn (): bool => false),
        ], limit: 1, category: 'Контакты', description: 'Заголовок, описание, телефон, email, адрес, карта iframe и форма (прототип /contacts)', icon: 'phone');
    }
}
