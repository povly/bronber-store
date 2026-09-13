<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use App\Services\Languages\LanguageService;
use App\Services\Settings\SettingService;
use Illuminate\Support\Facades\Log;

/**
 * Turns header/footer/mobile settings JSON (flexible-layouts blocks)
 * into flat view-ready structures with resolved link hrefs.
 *
 * A missing block yields null for its key — the static markup then renders
 * its hardcoded fallback (nothing breaks when settings are empty).
 */
class SettingsResolver
{
    /**
     * Pure-media setting blocks inherited wholesale for non-default
     * locales when the locale lacks them entirely.
     *
     * @var list<string>
     */
    private const MEDIA_BLOCK_TYPES = ['logo', 'payment'];

    /**
     * Header structures for the layout partials.
     *
     * @return array{
     *     topBar: array{phone: ?string, links: list<array{label: string, href: string}>}|null,
     *     nav: array{links: list<array{label: string, href: string}>}|null,
     *     logo: array{image: string}|null,
     * }
     */
    public static function header(?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        $blocks = self::withMediaFallback(
            resolve(SettingService::class)->get('header', $locale),
            'header',
            $locale,
        );

        $topBar = null;
        $nav = null;
        $logo = null;
        $linksCount = 0;

        foreach ($blocks as $block) {
            $type = $block['_type'] ?? null;

            if ($type === 'logo') {
                $image = self::media($block, 'image');
                $logo = $image !== null ? ['image' => $image] : null;
            }

            if ($type === 'top-bar') {
                $links = self::links((array) ($block['links'] ?? []), $locale);
                $topBar = [
                    'phone' => self::string($block, 'phone'),
                    'links' => $links,
                ];
                $linksCount += count($links);
            }

            if ($type === 'nav') {
                $links = self::links((array) ($block['links'] ?? []), $locale);
                $nav = ['links' => $links];
                $linksCount += count($links);
            }
        }

        Log::debug('[SettingsResolver] context={context} blocks={blocks} links={links}', [
            'context' => 'header',
            'blocks' => count($blocks),
            'links' => $linksCount,
        ]);

        return ['topBar' => $topBar, 'nav' => $nav, 'logo' => $logo];
    }

    /**
     * Footer structures for the layout partials.
     *
     * @return array{
     *     logo: array{image: string}|null,
     *     contacts: array{phone: ?string, email: ?string}|null,
     *     contactItems: list<array{icon: ?string, text: string, href: ?string}>|null,
     *     socials: list<array{platform: string, url: string, icon: ?string}>|null,
     *     columns: list<array{title: ?string, links: list<array{label: string, href: string}>}>,
     *     payment: array{image: string}|null,
     *     bottom: array{links: list<array{label: string, href: string}>, copyright: ?string,
     *         developer_label: ?string, developer_url: ?string}|null,
     * }
     */
    public static function footer(?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        $blocks = self::withMediaFallback(
            resolve(SettingService::class)->get('footer', $locale),
            'footer',
            $locale,
        );

        $logo = null;
        $contacts = null;
        $contactItems = null;
        $socials = null;
        $columns = [];
        $payment = null;
        $bottom = null;
        $linksCount = 0;

        foreach ($blocks as $block) {
            $type = $block['_type'] ?? null;

            if ($type === 'logo') {
                $image = self::media($block, 'image');
                $logo = $image !== null ? ['image' => $image] : null;
            }

            if ($type === 'contacts') {
                $contacts = [
                    'phone' => self::string($block, 'phone'),
                    'email' => self::string($block, 'email'),
                ];

                $items = [];

                foreach ((array) ($block['items'] ?? []) as $item) {
                    if (! is_array($item)) {
                        continue;
                    }

                    $text = self::string($item, 'text');

                    if ($text === null) {
                        continue;
                    }

                    $items[] = [
                        'icon' => self::media($item, 'icon'),
                        'text' => $text,
                        'href' => self::string($item, 'href'),
                    ];
                }

                if ($items !== []) {
                    $contactItems = $items;
                }
            }

            if ($type === 'socials') {
                $socials = [];

                foreach ((array) ($block['links'] ?? []) as $social) {
                    $platform = self::string($social, 'platform');
                    $url = self::string($social, 'url');

                    if ($platform !== null && $url !== null) {
                        $socials[] = [
                            'platform' => $platform,
                            'url' => $url,
                            'icon' => self::media($social, 'icon'),
                        ];
                    }
                }
            }

            if ($type === 'links-column') {
                $links = self::links((array) ($block['links'] ?? []), $locale);
                $columns[] = [
                    'title' => self::string($block, 'title'),
                    'links' => $links,
                ];
                $linksCount += count($links);
            }

            if ($type === 'payment') {
                $image = self::media($block, 'image');
                $payment = $image !== null ? ['image' => $image] : null;
            }

            if ($type === 'bottom') {
                $bottomLinks = self::links((array) ($block['links'] ?? []), $locale);

                // Legacy privacy/terms fields → links (data saved before
                // the bottom row switched to the Json links list).
                if ($bottomLinks === []) {
                    foreach (['privacy', 'terms'] as $legal) {
                        $label = self::string($block, $legal.'_label');

                        if ($label !== null) {
                            $bottomLinks[] = [
                                'label' => $label,
                                'href' => self::string($block, $legal.'_url') ?? '#',
                            ];
                        }
                    }
                }

                $bottom = [
                    'links' => $bottomLinks,
                    'copyright' => self::string($block, 'copyright'),
                    'developer_label' => self::string($block, 'developer_label'),
                    'developer_url' => self::string($block, 'developer_url'),
                ];
            }
        }

        Log::debug('[SettingsResolver] context={context} blocks={blocks} links={links}', [
            'context' => 'footer',
            'blocks' => count($blocks),
            'links' => $linksCount,
        ]);

        return [
            'logo' => $logo,
            'contacts' => $contacts,
            'contactItems' => $contactItems,
            'socials' => $socials,
            'columns' => $columns,
            'payment' => $payment,
            'bottom' => $bottom,
        ];
    }

    /**
     * Mobile drawer menu structures for the layout partials. Each «links»
     * block is a separate link list — the view renders dividers between
     * them (three lists in the static prototype).
     *
     * @return array{
     *     links: list<list<array{label: string, href: string}>>|null,
     *     logo: array{image: string}|null,
     *     contacts: list<array{icon: ?string, text: string, href: ?string}>|null,
     * }
     */
    public static function mobileMenu(?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        $blocks = self::withMediaFallback(
            resolve(SettingService::class)->get('mobile-menu', $locale),
            'mobile-menu',
            $locale,
        );

        $linkGroups = [];
        $logo = null;
        $contacts = null;

        foreach ($blocks as $block) {
            $type = $block['_type'] ?? null;

            if ($type === 'links') {
                $resolved = self::links((array) ($block['links'] ?? []), $locale);

                if ($resolved !== []) {
                    $linkGroups[] = $resolved;
                }
            }

            if ($type === 'logo') {
                $image = self::media($block, 'image');
                $logo = $image !== null ? ['image' => $image] : null;
            }

            if ($type === 'contacts') {
                $items = [];

                foreach ((array) ($block['items'] ?? []) as $item) {
                    if (! is_array($item)) {
                        continue;
                    }

                    $text = self::string($item, 'text');

                    if ($text === null) {
                        continue;
                    }

                    $items[] = [
                        'icon' => self::media($item, 'icon'),
                        'text' => $text,
                        'href' => self::string($item, 'href'),
                    ];
                }

                if ($items !== []) {
                    $contacts = $items;
                }
            }
        }

        Log::debug('[SettingsResolver] context={context} blocks={blocks} links={links}', [
            'context' => 'mobile-menu',
            'blocks' => count($blocks),
            'links' => array_sum(array_map('count', $linkGroups)),
        ]);

        return ['links' => $linkGroups === [] ? null : $linkGroups, 'logo' => $logo, 'contacts' => $contacts];
    }

    /**
     * Mobile bottom nav structures for the layout partials. Each item is
     * an icon + a resolved two-type link; the functional catalog button
     * is inserted by the view, not by settings.
     *
     * @return array{
     *     items: list<array{icon: ?string, label: string, href: string}>|null,
     * }
     */
    public static function mobileNav(?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        $blocks = self::withMediaFallback(
            resolve(SettingService::class)->get('mobile-nav', $locale),
            'mobile-nav',
            $locale,
        );

        $items = null;

        foreach ($blocks as $block) {
            if (($block['_type'] ?? null) !== 'items') {
                continue;
            }

            $resolved = [];

            foreach ((array) ($block['items'] ?? []) as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $label = self::string($item, 'label');
                $href = LinkResolver::href($item, $locale);

                if ($label !== null && $href !== null) {
                    $resolved[] = [
                        'icon' => self::media($item, 'icon'),
                        'label' => $label,
                        'href' => $href,
                    ];
                }
            }

            if ($resolved !== []) {
                $items = $resolved;
            }
        }

        Log::debug('[SettingsResolver] context={context} blocks={blocks} links={links}', [
            'context' => 'mobile-nav',
            'blocks' => count($blocks),
            'links' => count($items ?? []),
        ]);

        return ['items' => $items];
    }

    /**
     * Fill empty media fields of a non-default locale's setting from the
     * default locale's setting; pure-media blocks (logo, payment) are
     * inherited wholesale when the locale lacks them. Media is owned by
     * the primary language — text content stays per-locale.
     *
     * @param  list<array<string, mixed>>  $blocks
     * @return list<array<string, mixed>>
     */
    private static function withMediaFallback(array $blocks, string $key, string $locale): array
    {
        $default = resolve(LanguageService::class)->defaultCode();

        if ($locale === $default) {
            return $blocks;
        }

        return MediaFallback::apply(
            $blocks,
            resolve(SettingService::class)->get($key, $default),
            self::MEDIA_BLOCK_TYPES,
        );
    }

    /**
     * Resolve a list of two-type links to {label, href}, skipping broken ones.
     *
     * @param  list<array<string, mixed>>  $links
     * @return list<array{label: string, href: string}>
     */
    private static function links(array $links, ?string $locale): array
    {
        $resolved = [];

        foreach ($links as $link) {
            if (! is_array($link)) {
                continue;
            }

            $label = self::string($link, 'label');
            $href = LinkResolver::href($link, $locale);

            if ($label !== null && $href !== null) {
                $resolved[] = ['label' => $label, 'href' => $href];
            }
        }

        return $resolved;
    }

    /**
     * Trimmed non-empty string value or null.
     *
     * @param  array<string, mixed>  $data
     */
    private static function string(array $data, string $key): ?string
    {
        $value = trim((string) ($data[$key] ?? ''));

        return $value === '' ? null : $value;
    }

    /**
     * Normalized media path: disk-relative picker paths get the
     * /storage/ prefix; absolute paths and URLs pass through; empty → null.
     *
     * @param  array<string, mixed>  $data
     */
    private static function media(array $data, string $key): ?string
    {
        $path = trim((string) ($data[$key] ?? ''));

        if ($path === '') {
            return null;
        }

        return str_starts_with($path, '/') || str_starts_with($path, 'http')
            ? $path
            : '/storage/'.ltrim($path, '/');
    }
}
