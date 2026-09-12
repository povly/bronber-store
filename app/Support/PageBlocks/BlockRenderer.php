<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Renders flexible-layouts JSON blocks ({_type, ...fields}) to storefront HTML.
 *
 * Each block maps to a blade view `components.page-blocks.{context}.{type}`
 * (context: page, header, footer). Unknown or failing blocks are logged and
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
            $view = "components.page-blocks.{$context}.{$type}";

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
}
