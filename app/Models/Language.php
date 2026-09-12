<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Languages\LanguageService;
use Database\Factories\LanguageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'name', 'sort_order', 'is_default'])]
class Language extends Model
{
    /**
     * @use HasFactory<LanguageFactory>
     */
    use HasFactory;

    /**
     * Bootstrap the model and flush the language cache on any change,
     * so routes, middleware and locale fallbacks pick up edits immediately.
     */
    protected static function booted(): void
    {
        static::saved(fn () => resolve(LanguageService::class)->clearCache());
        static::deleted(fn () => resolve(LanguageService::class)->clearCache());
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
            'sort_order' => 'integer',
            'is_default' => 'boolean',
        ];
    }
}
