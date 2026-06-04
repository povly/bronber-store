<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app['view']->addLocation(resource_path('blocks'));

        View::composer('common.header.header', function (\Illuminate\View\View $view) {
            $view->with('searchTypes', config('search.types'));
        });
    }
}
