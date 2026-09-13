<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use App\Support\PageBlocks\Concerns\BuildsLinkFields;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Text;
use Povly\FlexibleLayouts\Fields\FlexibleLayouts;
use YuriZoom\MoonShineMediaManager\Fields\MediaManagerPicker;

/**
 * Flexible-layouts block definitions for the footer setting
 * (settings.value, key=footer).
 *
 * Blocks mirror the static footer markup: logo, contacts (flexible
 * icon+text items), socials with icons, link columns (accordions),
 * payment image and the bottom legal row. The auth button stays in
 * code — it is functional, not content.
 */
class FooterBlockLibrary
{
    use BuildsLinkFields;

    /**
     * Block set for the footer setting.
     *
     * An optional prebuilt field lets other libraries append their blocks
     * onto the same instance (see SettingFormPage's AJAX fallback).
     */
    public static function footer(?FlexibleLayouts $layouts = null): FlexibleLayouts
    {
        $layouts ??= FlexibleLayouts::make('Значение', 'value');

        return $layouts
            ->block('logo', 'Логотип', [
                MediaManagerPicker::make('Изображение', 'image')
                    ->allowedExtensions(['svg', 'png', 'webp', 'avif', 'jpg', 'jpeg']),
            ], limit: 1, category: 'Подвал', description: 'Логотип подвала (SVG выводится инлайном; пусто — статический логотип)', icon: 'photo')
            ->block('contacts', 'Контакты', [
                Json::make('Пункты', 'items')
                    ->fields([
                        MediaManagerPicker::make('Иконка', 'icon')
                            ->allowedExtensions(['svg', 'png', 'webp', 'avif', 'jpg', 'jpeg']),
                        Text::make('Текст', 'text')
                            ->required()
                            ->escapeOnApply(static fn (): bool => false),
                        Text::make('Ссылка (необязательно)', 'href')
                            ->hint('Например: tel:+7…, mailto:… или https://…')
                            ->escapeOnApply(static fn (): bool => false),
                    ]),
            ], limit: 1, category: 'Подвал', description: 'Гибкий блок контактов: иконка + текст + ссылка', icon: 'phone')
            ->block('socials', 'Соцсети', [
                Json::make('Ссылки', 'links')
                    ->fields([
                        Text::make('Платформа', 'platform')
                            ->hint('Например: Instagram, YouTube, Telegram')
                            ->escapeOnApply(static fn (): bool => false),
                        Text::make('URL', 'url')
                            ->escapeOnApply(static fn (): bool => false),
                        MediaManagerPicker::make('Иконка', 'icon')
                            ->allowedExtensions(['svg', 'png', 'webp', 'avif', 'jpg', 'jpeg']),
                    ]),
            ], limit: 1, category: 'Подвал', description: 'Иконки соцсетей (пусто — аббревиатура платформы)', icon: 'globe-alt')
            ->block('links-column', 'Колонка ссылок', [
                Text::make('Заголовок колонки', 'title')
                    ->escapeOnApply(static fn (): bool => false),
                Json::make('Ссылки', 'links')
                    ->fields(self::linkFields()),
            ], limit: 3, category: 'Подвал', description: 'Колонка-аккордеон со ссылками (до 3 колонок)', icon: 'link')
            ->block('payment', 'Оплата', [
                MediaManagerPicker::make('Изображение', 'image')
                    ->allowedExtensions(['svg', 'png', 'webp', 'avif', 'jpg', 'jpeg']),
            ], limit: 1, category: 'Подвал', description: 'Изображение способов оплаты (пусто — статические бейджи)', icon: 'credit-card')
            ->block('bottom', 'Нижняя строка', [
                Json::make('Ссылки', 'links')
                    ->fields(self::linkFields())
                    ->hint('Юридические ссылки нижней строки'),
                Text::make('Копирайт', 'copyright')
                    ->hint('Например: © 2026 Bronber Store')
                    ->escapeOnApply(static fn (): bool => false),
                Text::make('Разработчик — название', 'developer_label')
                    ->escapeOnApply(static fn (): bool => false),
                Text::make('Разработчик — URL', 'developer_url')
                    ->escapeOnApply(static fn (): bool => false),
            ], limit: 1, category: 'Подвал', description: 'Юридические ссылки, копирайт и разработчик', icon: 'cog-6-tooth');
    }
}
