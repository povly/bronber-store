<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use Illuminate\Support\Facades\Log;

/**
 * Fills empty media fields of locale-scoped flexible-layout content
 * (page translations, header/footer settings) from the default locale's
 * content («primary language» fallback).
 *
 * Media fields are DECLARED per block type by the caller — a schema map
 * (type => list of slots): a string slot is a block-level media key,
 * '<list>' => [keys] declares item-level media keys inside that list.
 * A block type without a schema is left untouched — nothing is scanned
 * or filled globally.
 *
 * Blocks are matched by {_type} + occurrence order; items inside a
 * block (e.g. contacts items, socials links) are matched by index.
 * Pure-media blocks (logo, payment) are inherited wholesale when the
 * locale lacks the block at all.
 */
final class MediaFallback
{
    /**
     * Block-level media keys that hold a list value (a JSON-encoded
     * string or an array) instead of a single path.
     *
     * @var list<string>
     */
    private const LIST_VALUED_KEYS = ['images'];

    /**
     * Apply the fallback to a locale's content.
     *
     * @param  list<array<string, mixed>>  $content
     * @param  list<array<string, mixed>>|null  $fallbackContent
     * @param  array<string, array<string|int, mixed>>  $schemas  media slots per block type
     * @param  list<string>  $inheritMissingTypes  pure-media block types inherited
     *                                             wholesale when the locale lacks them
     * @return list<array<string, mixed>>
     */
    public static function apply(array $content, ?array $fallbackContent, array $schemas = [], array $inheritMissingTypes = []): array
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
            $schema = $schemas[$type] ?? null;

            if ($schema === null) {
                continue;
            }

            $occurrence = $seen[$type] ?? 0;
            $seen[$type] = $occurrence + 1;

            $fallback = $fallbackByType[$type][$occurrence] ?? null;

            if (is_array($fallback)) {
                $content[$index] = self::fillBlock($block, $fallback, $type, $schema);
            }
        }

        if ($inheritMissingTypes !== []) {
            $content = self::inheritMissingBlocks($content, $fallbackContent, $inheritMissingTypes, $schemas);
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
     * @param  array<string, array<string|int, mixed>>  $schemas
     * @return list<array<string, mixed>>
     */
    private static function inheritMissingBlocks(array $content, array $fallbackContent, array $types, array $schemas): array
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

            if (in_array($type, $present, true) || ! self::blockHasMedia($block, $schemas[$type] ?? [])) {
                continue;
            }

            Log::debug('[MediaFallback] {type} block missing in this locale, inherited from the default one', ['type' => $type]);

            $content[] = $block;
            $present[] = $type;
        }

        return $content;
    }

    /**
     * Whether the block carries at least one non-empty declared media
     * value (block-level or inside declared lists).
     *
     * @param  array<string, mixed>  $block
     * @param  array<string|int, mixed>  $schema
     */
    private static function blockHasMedia(array $block, array $schema): bool
    {
        foreach ($schema as $key => $slot) {
            if (is_string($key)) {
                foreach ((array) ($block[$key] ?? []) as $item) {
                    if (is_array($item) && self::itemHasMedia($item, (array) $slot)) {
                        return true;
                    }
                }

                continue;
            }

            if (! self::isEmptyMedia($block[(string) $slot] ?? null, self::isListKey((string) $slot))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $item
     * @param  list<string>  $itemKeys
     */
    private static function itemHasMedia(array $item, array $itemKeys): bool
    {
        foreach ($itemKeys as $itemKey) {
            if (! self::isEmptyMedia($item[$itemKey] ?? null, false)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Copy non-empty declared media values from the fallback block into
     * the empty media slots of the current block.
     *
     * @param  array<string, mixed>  $block
     * @param  array<string, mixed>  $fallback
     * @param  array<string|int, mixed>  $schema
     * @return array<string, mixed>
     */
    private static function fillBlock(array $block, array $fallback, string $type, array $schema): array
    {
        foreach ($schema as $key => $slot) {
            if (is_string($key)) {
                $block = self::fillList($block, $fallback, $type, $key, (array) $slot);

                continue;
            }

            $block = self::fillSlot($block, $fallback, "{$type}.{$slot}", (string) $slot);
        }

        return $block;
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
     * Fill empty media slots of a declared list's items, matched by index.
     *
     * @param  array<string, mixed>  $block
     * @param  array<string, mixed>  $fallback
     * @param  list<string>  $itemKeys
     * @return array<string, mixed>
     */
    private static function fillList(array $block, array $fallback, string $type, string $listKey, array $itemKeys): array
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

            $filled = $item;

            foreach ($itemKeys as $itemKey) {
                $filled = self::fillSlot($filled, $fallbackItem, "{$type}.{$listKey}.{$index}.{$itemKey}", $itemKey);
            }

            $items[$index] = $filled;
        }

        $block[$listKey] = $items;

        return $block;
    }

    /**
     * Whether the media key holds a list value (affects emptiness rules).
     */
    private static function isListKey(string $key): bool
    {
        return in_array($key, self::LIST_VALUED_KEYS, true);
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
