<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use Illuminate\Support\Facades\Log;

/**
 * Fills empty media fields of locale-scoped flexible-layout content
 * (page translations, header/footer settings) from the default locale's
 * content («primary language» fallback).
 *
 * A locale may exist while its media fields are empty — images are
 * managed per language in MoonShine. Instead of duplicating files between
 * translations, empty media fields inherit the default locale's value at
 * render time. Pure-media blocks (logo, payment) are inherited wholesale
 * when the locale lacks the block at all.
 *
 * Blocks are matched by {_type} + occurrence order; items inside a
 * block (e.g. home-advs, contacts items, socials links) are matched by
 * index.
 */
final class MediaFallback
{
    /**
     * Single-value media keys: a plain path string is a valid value.
     */
    private const SINGLE_MEDIA_KEYS = ['image', 'icon'];

    /**
     * List media keys: a JSON-encoded string or an array; a non-JSON
     * string is treated as empty — the templates skip such values.
     */
    private const LIST_MEDIA_KEYS = ['images'];

    /**
     * List fields whose items may carry media keys.
     */
    private const LIST_KEYS = ['items', 'links'];

    /**
     * Apply the fallback to a locale's content.
     *
     * @param  list<array<string, mixed>>  $content
     * @param  list<array<string, mixed>>|null  $fallbackContent
     * @param  list<string>  $inheritMissingTypes  pure-media block types inherited
     *                                             wholesale when the locale lacks them
     * @return list<array<string, mixed>>
     */
    public static function apply(array $content, ?array $fallbackContent, array $inheritMissingTypes = []): array
    {
        if ($fallbackContent === null) {
            return $content;
        }

        $fallbackByType = [];

        foreach ($fallbackContent as $block) {
            if (is_array($block) && array_key_exists('_type', $block)) {
                $fallbackByType[(string) $block['_type']][] = $block;
            }
        }

        $seen = [];

        foreach ($content as $index => $block) {
            if (! is_array($block) || ! array_key_exists('_type', $block)) {
                continue;
            }

            $type = (string) $block['_type'];
            $occurrence = $seen[$type] ?? 0;
            $seen[$type] = $occurrence + 1;

            $fallback = $fallbackByType[$type][$occurrence] ?? null;

            if (is_array($fallback)) {
                $content[$index] = self::fillBlock($block, $fallback, $type);
            }
        }

        if ($inheritMissingTypes !== []) {
            $content = self::inheritMissingBlocks($content, $fallbackContent, $inheritMissingTypes);
        }

        return $content;
    }

    /**
     * Append pure-media blocks (logo, payment) that the locale lacks
     * at all, but only when the default locale's block actually
     * carries media — an empty default block is never inherited.
     *
     * @param  list<array<string, mixed>>  $content
     * @param  list<array<string, mixed>>  $fallbackContent
     * @param  list<string>  $types
     * @return list<array<string, mixed>>
     */
    private static function inheritMissingBlocks(array $content, array $fallbackContent, array $types): array
    {
        $present = [];

        foreach ($content as $block) {
            if (is_array($block) && in_array($block['_type'] ?? null, $types, true)) {
                $present[] = (string) $block['_type'];
            }
        }

        foreach ($fallbackContent as $block) {
            if (! is_array($block) || ! in_array($block['_type'] ?? null, $types, true)) {
                continue;
            }

            $type = (string) $block['_type'];

            if (in_array($type, $present, true) || ! self::blockHasMedia($block)) {
                continue;
            }

            Log::debug('[MediaFallback] {type} block missing in this locale, inherited from the default one', ['type' => $type]);

            $content[] = $block;
            $present[] = $type;
        }

        return $content;
    }

    /**
     * Whether the block carries at least one non-empty media value
     * (block-level or inside list items/links).
     *
     * @param  array<string, mixed>  $block
     */
    private static function blockHasMedia(array $block): bool
    {
        foreach (self::mediaKeys() as $key) {
            if (! self::isEmptyMedia($block[$key] ?? null, self::isListKey($key))) {
                return true;
            }
        }

        foreach (self::LIST_KEYS as $listKey) {
            foreach ((array) ($block[$listKey] ?? []) as $item) {
                if (! is_array($item)) {
                    continue;
                }

                foreach (self::mediaKeys() as $key) {
                    if (! self::isEmptyMedia($item[$key] ?? null, self::isListKey($key))) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Copy non-empty media values from the fallback block into the
     * empty media slots of the current block.
     *
     * @param  array<string, mixed>  $block
     * @param  array<string, mixed>  $fallback
     * @return array<string, mixed>
     */
    private static function fillBlock(array $block, array $fallback, string $type): array
    {
        foreach (self::mediaKeys() as $key) {
            $block = self::fillSlot($block, $fallback, "{$type}.{$key}", $key);
        }

        foreach (self::LIST_KEYS as $listKey) {
            $block = self::fillList($block, $fallback, $type, $listKey);
        }

        return $block;
    }

    /**
     * All media keys participating in the fallback.
     *
     * @return list<string>
     */
    private static function mediaKeys(): array
    {
        return [...self::SINGLE_MEDIA_KEYS, ...self::LIST_MEDIA_KEYS];
    }

    /**
     * Whether the media key holds a list value (affects emptiness rules).
     */
    private static function isListKey(string $key): bool
    {
        return in_array($key, self::LIST_MEDIA_KEYS, true);
    }

    /**
     * Fill one media slot on the block level.
     *
     * @param  array<string, mixed>  $block
     * @param  array<string, mixed>  $fallback
     * @return array<string, mixed>
     */
    private static function fillSlot(array $block, array $fallback, string $path, string $key): array
    {
        $isList = self::isListKey($key);

        if (! self::isEmptyMedia($block[$key] ?? null, $isList)) {
            return $block;
        }

        $value = $fallback[$key] ?? null;

        if (self::isEmptyMedia($value, $isList)) {
            return $block;
        }

        Log::debug('[MediaFallback] {path} empty in this locale, inherited from the default one', ['path' => $path]);

        $block[$key] = $value;

        return $block;
    }

    /**
     * Fill empty media slots of list items, matched by index.
     *
     * @param  array<string, mixed>  $block
     * @param  array<string, mixed>  $fallback
     * @return array<string, mixed>
     */
    private static function fillList(array $block, array $fallback, string $type, string $listKey): array
    {
        $items = $block[$listKey] ?? null;

        if (! is_array($items)) {
            return $block;
        }

        $fallbackItems = is_array($fallback[$listKey] ?? null) ? $fallback[$listKey] : [];

        foreach ($items as $index => $item) {
            $fallbackItem = $fallbackItems[$index] ?? null;

            if (! is_array($item) || ! is_array($fallbackItem)) {
                continue;
            }

            // Chain fills across media keys: every fillSlot call must
            // receive the result of the previous one — the original
            // $item is a foreach snapshot and would clobber earlier
            // fills on the next key pass.
            $filled = $item;

            foreach (self::mediaKeys() as $key) {
                $filled = self::fillSlot($filled, $fallbackItem, "{$type}.{$listKey}.{$index}.{$key}", $key);
            }

            $items[$index] = $filled;
        }

        $block[$listKey] = $items;

        return $block;
    }

    /**
     * «Empty» mirrors the template normalization: null, '' or an empty
     * list. List values accept a JSON-encoded string — a non-JSON one
     * counts as empty there (the partners template skips such values).
     * Single values accept any non-empty string as a valid path.
     */
    private static function isEmptyMedia(mixed $value, bool $list): bool
    {
        if (is_array($value)) {
            return $value === [];
        }

        if (is_string($value) && $value !== '') {
            if (! $list) {
                return false;
            }

            $decoded = json_decode($value, true);

            return ! is_array($decoded) || $decoded === [];
        }

        return true;
    }
}
