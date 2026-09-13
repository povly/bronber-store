<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Renders flexible-layouts JSON blocks ({_type, ...fields}) to storefront HTML.
 *
 * Each block type maps to a blade view: «{page}-{name}» →
 * `blocks/{page}/{name}` (home-partners → blocks/home/partners);
 * legacy demo types (no page prefix, incl. dashed «featured-products»)
 * → `blocks/legacy/{type}`. Unknown or failing blocks are logged and
 * skipped — a broken block must never take the whole page down.
 */
class BlockRenderer
{
    /**
     * Render a list of blocks into concatenated HTML.
     *
     * @param  list<array<string, mixed>>  $blocks
     */
    public function render(array $blocks, string $context = 'page'): string
    {
        $html = '';

        foreach ($blocks as $block) {
            if (! is_array($block) || ! array_key_exists('_type', $block)) {
                Log::warning('[BlockRenderer] malformed block skipped', ['context' => $context]);

                continue;
            }

            $type = (string) $block['_type'];
            $view = $this->viewFor($type);

            if (! view()->exists($view)) {
                Log::warning('[BlockRenderer] unknown block type={type}, skipped', [
                    'type' => $type,
                    'context' => $context,
                ]);

                continue;
            }

            try {
                $html .= view($view, ['block' => $block])->render();
            } catch (Throwable $e) {
                Log::warning('[BlockRenderer] block render failed, skipped', [
                    'type' => $type,
                    'context' => $context,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $html;
    }

    /**
     * Legacy demo block types without a page prefix — rendered from
     * blocks/legacy/. A closed, frozen set.
     *
     * @var list<string>
     */
    private const LEGACY_TYPES = ['hero', 'text', 'gallery', 'contacts', 'faq', 'featured-products'];

    /**
     * View name for a block type: «{page}-{name}» maps to
     * blocks.{page}.{name} (home-partners → blocks/home/partners);
     * legacy demo types and unprefixed types map to blocks/legacy/{type}.
     */
    private function viewFor(string $type): string
    {
        if (in_array($type, self::LEGACY_TYPES, true) || ! str_contains($type, '-')) {
            return 'blocks.legacy.'.$type;
        }

        [$page, $name] = explode('-', $type, 2);

        return "blocks.{$page}.{$name}";
    }
}
