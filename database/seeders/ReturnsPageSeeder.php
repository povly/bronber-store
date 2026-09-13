<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * Editable returns page: publishes the DB page with slug «returns» so
 * /returns renders from a MoonShine-editable «returns-content» block —
 * the whole page body is a single EditorJS document. Content mirrors the
 * static prototype (blocks/returns/returns.blade.php); the wording is
 * pulled from lang/{ru,en}/returns.php at seed time so the prototype
 * stays the single source of the texts. Idempotent: firstOrCreate by
 * slug / (page_id, locale); an existing translation with EMPTY content
 * is filled with the demo data — authored content is never overwritten
 * (rule: .ai/rules/seeders.md).
 */
class ReturnsPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::query()->firstOrCreate(
            ['slug' => 'returns'],
            ['is_published' => true, 'sort_order' => 0],
        );

        $locales = [];

        foreach (['ru', 'en'] as $locale) {
            $translation = PageTranslation::query()->firstOrCreate(
                ['page_id' => $page->getKey(), 'locale' => $locale],
                $this->attributes($locale),
            );

            if ($this->isEmptyContent($translation)) {
                $translation->fill($this->attributes($locale))->save();

                $locales[] = $locale.':filled';

                continue;
            }

            $locales[] = $locale.($translation->wasRecentlyCreated ? ':created' : ':exists');
        }

        Log::info('[ReturnsPageSeeder] returns page seeded, locales={locales}', [
            'locales' => implode(', ', $locales),
        ]);
    }

    /**
     * Creation/fill attributes for a locale's demo translation.
     *
     * @return array<string, mixed>
     */
    private function attributes(string $locale): array
    {
        return [
            'title' => $locale === 'ru' ? 'Возврат и обмен' : 'Returns and Exchanges',
            'meta_title' => $locale === 'ru'
                ? 'Возврат и обмен — Bronber: условия возврата и обмена автозапчастей'
                : 'Returns and Exchanges — Bronber: auto parts return and exchange terms',
            'meta_description' => $locale === 'ru'
                ? 'Условия возврата и обмена автозапчастей Bronber: 14 дней на возврат товара надлежащего качества, перечень не подлежащих возврату товаров, гарантия на производственный брак.'
                : 'Bronber auto parts return and exchange terms: 14 days for a quality-goods return, the list of non-returnable goods, and the manufacturing defect warranty.',
            'content' => $this->content($locale),
        ];
    }

    /**
     * Page content: a single «returns-content» block whose body is the
     * EditorJS document built from the prototype wording.
     *
     * @return list<array<string, mixed>>
     */
    private function content(string $locale): array
    {
        return [
            [
                '_type' => 'returns-content',
                'title' => $this->text('title', $locale),
                'body' => $this->editorJs($locale),
            ],
        ];
    }

    /**
     * EditorJs payload mirroring the static prototype: two paragraphs,
     * the non-returnable h2, body_3 (intro paragraph + unordered list +
     * closing paragraphs), the defective-goods h2 and the last paragraph.
     * Same storage shape as the admin editor (AboutPageSeeder).
     */
    private function editorJs(string $locale): string
    {
        /** @var list<array<string, mixed>> $blocks */
        $blocks = [
            ['type' => 'paragraph', 'data' => ['text' => $this->text('body_1', $locale)]],
            ['type' => 'paragraph', 'data' => ['text' => $this->text('body_2', $locale)]],
            ['type' => 'header', 'data' => ['text' => $this->text('subtitle_non_returnable', $locale), 'level' => 2]],
        ];

        // body_3 packs three sub-parts into one \n-separated string:
        // line 0 — intro paragraph, lines 1-4 — list items, the rest —
        // closing paragraphs (empty lines are breaks, not blocks).
        $lines = explode("\n", $this->text('body_3', $locale));

        $blocks[] = ['type' => 'paragraph', 'data' => ['text' => trim($lines[0])]];

        $blocks[] = ['type' => 'list', 'data' => [
            'style' => 'unordered',
            'items' => array_map(
                static fn (string $item): string => trim($item),
                array_slice($lines, 1, 4),
            ),
        ]];

        foreach (array_slice($lines, 5) as $line) {
            if (trim($line) !== '') {
                $blocks[] = ['type' => 'paragraph', 'data' => ['text' => trim($line)]];
            }
        }

        $blocks[] = ['type' => 'header', 'data' => ['text' => $this->text('subtitle_defective', $locale), 'level' => 2]];
        $blocks[] = ['type' => 'paragraph', 'data' => ['text' => $this->text('body_4', $locale)]];

        return (string) json_encode([
            'time' => 0,
            'blocks' => $blocks,
            'version' => '1.0.0',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Prototype wording from the returns lang files of the locale.
     */
    private function text(string $key, string $locale): string
    {
        return (string) trans("returns.{$key}", [], $locale);
    }

    /**
     * A freshly created translation always carries content; a pre-existing
     * one is only "empty" when it has no blocks at all.
     */
    private function isEmptyContent(PageTranslation $translation): bool
    {
        return ! $translation->wasRecentlyCreated
            && ($translation->content === null || $translation->content === []);
    }
}
