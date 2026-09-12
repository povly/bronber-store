<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use App\Services\Settings\SettingService;
use Illuminate\Support\Facades\Log;

/**
 * Turns header/footer settings JSON (flexible-layouts blocks) into flat
 * view-ready structures with resolved link hrefs.
 *
 * A missing block yields null for its key — the static markup then renders
 * its hardcoded fallback (nothing breaks when settings are empty).
 */
class SettingsResolver
{
    /**
     * Header structures for the layout partials.
     *
     * @return array{
     *     topBar: array{phone: ?string, links: list<array{label: string, href: string}>}|null,
     *     nav: array{links: list<array{label: string, href: string}>}|null,
     * }
     */
    public static function header(?string $locale = null): array
    {
        $blocks = resolve(SettingService::class)->get('header', $locale);

        $topBar = null;
        $nav = null;
        $linksCount = 0;

        foreach ($blocks as $block) {
            $type = $block['_type'] ?? null;

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

        return ['topBar' => $topBar, 'nav' => $nav];
    }

    /**
     * Footer structures for the layout partials.
     *
     * @return array{
     *     contacts: array{phone: ?string, email: ?string}|null,
     *     socials: list<array{platform: string, url: string}>|null,
     *     columns: list<array{title: ?string, links: list<array{label: string, href: string}>}>,
     *     bottom: array{privacy_label: ?string, privacy_url: ?string, terms_label: ?string,
     *         terms_url: ?string, copyright: ?string, developer_label: ?string, developer_url: ?string}|null,
     * }
     */
    public static function footer(?string $locale = null): array
    {
        $blocks = resolve(SettingService::class)->get('footer', $locale);

        $contacts = null;
        $socials = null;
        $columns = [];
        $bottom = null;
        $linksCount = 0;

        foreach ($blocks as $block) {
            $type = $block['_type'] ?? null;

            if ($type === 'contacts') {
                $contacts = [
                    'phone' => self::string($block, 'phone'),
                    'email' => self::string($block, 'email'),
                ];
            }

            if ($type === 'socials') {
                $socials = [];

                foreach ((array) ($block['links'] ?? []) as $social) {
                    $platform = self::string($social, 'platform');
                    $url = self::string($social, 'url');

                    if ($platform !== null && $url !== null) {
                        $socials[] = ['platform' => $platform, 'url' => $url];
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

            if ($type === 'bottom') {
                $bottom = [
                    'privacy_label' => self::string($block, 'privacy_label'),
                    'privacy_url' => self::string($block, 'privacy_url'),
                    'terms_label' => self::string($block, 'terms_label'),
                    'terms_url' => self::string($block, 'terms_url'),
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
            'contacts' => $contacts,
            'socials' => $socials,
            'columns' => $columns,
            'bottom' => $bottom,
        ];
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
}
