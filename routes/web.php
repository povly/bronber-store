<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Application routes — defined once, auto-prefixed for non-default locales
|--------------------------------------------------------------------------
*/

$register = function () {
    Route::get('/', fn () => view('home'))->name('home');
    Route::get('/catalog', fn () => view('main'))->name('catalog');
};

// Default locale (no prefix)
Route::middleware('locale:'.config('app.available_locales.0'))->group($register);

// Non-default locales (/{locale} prefix)
foreach (array_slice(config('app.available_locales'), 1) as $locale) {
    Route::prefix($locale)
        ->name("{$locale}.")
        ->middleware("locale:{$locale}")
        ->group($register);
}
