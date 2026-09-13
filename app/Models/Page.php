<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Languages\LanguageService;
use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['slug', 'parent_id', 'is_published', 'sort_order'])]
class Page extends Model
{
    /**
     * @use HasFactory<PageFactory>
     */
    use HasFactory;

    /**
     * @return HasMany<PageTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(PageTranslation::class);
    }

    /**
     * Parent page (page hierarchy for breadcrumbs / subpage lists).
     *
     * @return BelongsTo<Page, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    /**
     * @return HasMany<Page, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    /**
     * Scope only published pages.
     *
     * @param  Builder<Page>  $builder
     */
    protected function scopePublished(Builder $builder): Builder
    {
        return $builder->where('is_published', true);
    }

    /**
     * Translation for the given locale with fallback: requested → default language → first available.
     */
    public function translation(?string $locale = null): ?PageTranslation
    {
        $locale ??= app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', resolve(LanguageService::class)->defaultCode())
            ?? $this->translations->first();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
