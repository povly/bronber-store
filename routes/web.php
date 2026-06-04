<?php

use Illuminate\Support\Facades\Route;

Route::get('/catalog', function () {
    return view('main');
})->name('catalog');

Route::get('/', function () {
    return view('main');
});
