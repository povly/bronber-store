<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use Sckatik\MoonshineEditorJs\Fields\EditorJs;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * Flexible-layouts block definitions for page content
 * (page_translations.content).
 *
 * Header and footer block sets live in their own libraries
 * ({@see HeaderBlockLibrary}, {@see FooterBlockLibrary}) — SOLID split by
 * context instead of one god-class.
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
                Text::make('Заголовок', 'title'),
                Textarea::make('Подзаголовок', 'subtitle'),
                MediaManagerPicker::make('Фоновое изображение', 'image')
                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'svg']),
            ], limit: 1, category: 'Контент', description: 'Крупный баннер с заголовком и картинкой', icon: 'photo')
            ->block('text', 'Текст (Editor.js)', [
                EditorJs::make('Тело блока', 'body'),
            ], category: 'Контент', description: 'Форматированный текстовый блок', icon: 'document-text')
            ->block('gallery', 'Галерея', [
                MediaManagerPicker::make('Изображения', 'images')
                    ->multiple()
                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'svg']),
            ], category: 'Медиа', description: 'Сетка изображений', icon: 'rectangle-stack')
            ->block('faq', 'Вопросы и ответы', [
                Json::make('Пункты', 'items')
                    ->fields([
                        Text::make('Вопрос', 'question'),
                        Textarea::make('Ответ', 'answer'),
                    ]),
            ], category: 'Контент', description: 'Список вопрос-ответ', icon: 'chat-bubble-left-right')
            ->block('contacts', 'Контакты', [
                Text::make('Адрес', 'address'),
                Text::make('Телефон', 'phone'),
                Text::make('E-mail', 'email'),
                Text::make('Ссылка на карту', 'map_url'),
            ], limit: 1, category: 'Контент', description: 'Адрес, телефон, ссылка на карту', icon: 'map-pin')
            ->block('featured-products', 'Товары (динамический)', [
                Text::make('Заголовок секции', 'title'),
                Number::make('Количество товаров', 'count')
                    ->min(1)
                    ->max(12)
                    ->default(4),
            ], category: 'Динамические', description: 'Данные берутся из каталога (сейчас mock, позже CRM-склад)', icon: 'cube');
    }
}
