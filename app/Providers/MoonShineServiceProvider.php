<?php

declare(strict_types=1);

namespace App\Providers;

use App\MoonShine\Resources\Article\ArticleResource;
use App\MoonShine\Resources\ArticleTranslation\ArticleTranslationResource;
use App\MoonShine\Resources\Language\LanguageResource;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRole\MoonShineUserRoleResource;
use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\PageTranslation\PageTranslationResource;
use App\MoonShine\Resources\Setting\SettingResource;
use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  CoreContract<MoonShineConfigurator>  $coreContract
     */
    public function boot(CoreContract $coreContract): void
    {
        $coreContract
            ->resources([
                MoonShineUserResource::class,
                MoonShineUserRoleResource::class,
                PageResource::class,
                ArticleResource::class,
                LanguageResource::class,
                PageTranslationResource::class,
                ArticleTranslationResource::class,
                SettingResource::class,
            ])
            ->pages([
                ...$coreContract->getConfig()->getPages(),
            ]);
    }
}
