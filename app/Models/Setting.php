<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Settings\SettingService;
use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'locale', 'value'])]
class Setting extends Model
{
    /**
     * @use HasFactory<SettingFactory>
     */
    use HasFactory;

    /**
     * Get a setting value by key and locale (fallback ru) through the cached SettingService.
     *
     * @return array<array-key, mixed>
     */
    public static function get(string $key, ?string $locale = null): array
    {
        return resolve(SettingService::class)->get($key, $locale);
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
            'value' => 'array',
        ];
    }
}
