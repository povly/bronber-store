<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use Sckatik\MoonshineEditorJs\Fields\EditorJs;

/**
 * Central catalogue of flexible-layouts block definitions.
 *
 * One place that describes every editable block for pages, header and footer.
 * Block field values are stored as JSON `[{_type: 'hero', ...fields}]` and
 * rendered on the storefront by {@see BlockRenderer}.
 */
class PageBlockLibrary
{
    /**
     * Block set for the page content field (page_translations.content).
     */
    public static function page(): FlexibleLayouts
    {
        return FlexibleLayouts::make('Контент', 'content')
            ->block('hero', 'Герой (баннер)', [
                Flex::make([
                    Column::make([
                        Text::make('Заголовок', 'title'),
                        Textarea::make('Подзаголовок', 'subtitle'),
                    ])->columnSpan(6),
                    Column::make([
                        Image::make('Фоновое изображение', 'image'),
                    ])->columnSpan(6),
                ]),
            ], limit: 1, category: 'Контент', description: 'Крупный баннер с заголовком и картинкой', icon: 'photo')
            ->block('text', 'Текст (Editor.js)', [
                EditorJs::make('Тело блока', 'body'),
            ], category: 'Контент', description: 'Форматированный текстовый блок', icon: 'document-text')
            ->block('gallery', 'Галерея', [
                Json::make('Изображения', 'images')
                    ->fields([
                        Image::make('Изображение', 'src'),
                    ]),
            ], category: 'Медиа', description: 'Сетка изображений', icon: 'rectangle-stack')
            ->block('faq', 'Вопросы и ответы', [
                Json::make('Пункты', 'items')
                    ->fields([
                        Text::make('Вопрос', 'question'),
                        Textarea::make('Ответ', 'answer'),
                    ]),
            ], category: 'Контент', description: 'Список вопрос-ответ', icon: 'chat-bubble-left-right')
            ->block('contacts', 'Контакты', [
                Flex::make([
                    Column::make([
                        Text::make('Адрес', 'address'),
                        Text::make('Телефон', 'phone'),
                    ])->columnSpan(6),
                    Column::make([
                        Text::make('E-mail', 'email'),
                        Text::make('Ссылка на карту', 'map_url'),
                    ])->columnSpan(6),
                ]),
            ], limit: 1, category: 'Контент', description: 'Адрес, телефон, ссылка на карту', icon: 'map-pin')
            ->block('featured-products', 'Товары (динамический)', [
                Flex::make([
                    Column::make([
                        Text::make('Заголовок секции', 'title'),
                    ])->columnSpan(6),
                    Column::make([
                        Number::make('Количество товаров', 'count')
                            ->min(1)
                            ->max(12)
                            ->default(4),
                    ])->columnSpan(6),
                ]),
            ], category: 'Динамические', description: 'Данные берутся из каталога (сейчас mock, позже CRM-склад)', icon: 'cube');
    }

    /**
     * Block set for the header setting (settings.value, key=header).
     */
    public static function header(): FlexibleLayouts
    {
        return FlexibleLayouts::make('Значение', 'value')
            ->block('nav', 'Навигация', [
                Json::make('Ссылки меню', 'links')
                    ->fields([
                        Text::make('Название', 'label'),
                        Text::make('URL', 'url'),
                    ]),
            ], limit: 1, category: 'Шапка', description: 'Основное меню шапки', icon: 'bars-3')
            ->block('contacts', 'Контакты в шапке', [
                Flex::make([
                    Column::make([
                        Text::make('Телефон', 'phone'),
                    ])->columnSpan(6),
                    Column::make([
                        Text::make('E-mail', 'email'),
                    ])->columnSpan(6),
                ]),
            ], limit: 1, category: 'Шапка', description: 'Телефон и почта в шапке', icon: 'phone');
    }

    /**
     * Block set for the footer setting (settings.value, key=footer).
     */
    public static function footer(): FlexibleLayouts
    {
        return FlexibleLayouts::make('Значение', 'value')
            ->block('links', 'Колонка ссылок', [
                Text::make('Заголовок колонки', 'title'),
                Json::make('Ссылки', 'links')
                    ->fields([
                        Text::make('Название', 'label'),
                        Text::make('URL', 'url'),
                    ]),
            ], category: 'Подвал', description: 'Колонка ссылок в подвале', icon: 'link')
            ->block('socials', 'Соцсети', [
                Json::make('Ссылки', 'links')
                    ->fields([
                        Text::make('Платформа', 'platform'),
                        Text::make('URL', 'url'),
                    ]),
            ], limit: 1, category: 'Подвал', description: 'Иконки соцсетей', icon: 'globe-alt')
            ->block('copyright', 'Копирайт', [
                Text::make('Текст', 'text'),
            ], limit: 1, category: 'Подвал', description: 'Строка копирайта', icon: 'cog-6-tooth');
    }
}
