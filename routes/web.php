<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Application routes — defined once, auto-prefixed for non-default locales
|--------------------------------------------------------------------------
*/

$register = function () {
    Route::get('/', fn () => view('home'))->name('home');
    Route::get('/catalog', fn () => view('main'))->name('catalog');

    Route::get('/cart', function () {
        $items = [
            ['id' => 1, 'image' => '/images/cart/product.png', 'title' => 'DeatschWerks 9-651-1008', 'article' => '0 580 464 070', 'brand' => 'DeatschWerks', 'price' => 1100, 'qty' => 1],
            ['id' => 2, 'image' => '/images/cart/product.png', 'title' => 'DeatschWerks 9-651-1008', 'article' => '0 580 464 070', 'brand' => 'DeatschWerks', 'price' => 1100, 'qty' => 2],
            ['id' => 3, 'image' => '/images/cart/product.png', 'title' => 'DeatschWerks 9-651-1008', 'article' => '0 580 464 070', 'brand' => 'DeatschWerks', 'price' => 1100, 'qty' => 2],
        ];

        $format = static fn (int $price) => number_format($price, 0, ',', ' ').' ₽';

        return view('cart', ['items' => $items, 'formatPrice' => $format]);
    })->name('cart');

    Route::get('/checkout', function () {
        $itemsCount = 5;
        $subtotal = 6600;
        $total = $subtotal;
        $format = static fn (int $price) => number_format($price, 0, ',', ' ').' ₽';

        return view('checkout', [
            'itemsCount' => $itemsCount,
            'subtotalFormatted' => $format($subtotal),
            'deliveryLabel' => 'Уточняется',
            'totalFormatted' => $format($total),
        ]);
    })->name('checkout');

    Route::get('/product', function () {
        $product = [
            'title' => 'Топливный насос Bosch',
            'article' => '0 580 464 070',
            'price' => 1100,
            'oldPrice' => 1300,
            'savings' => 200,
            'bonusPoints' => 110,
            'rating' => 8,
            'reviewCount' => 8,
            'inStock' => true,
            'isOriginal' => true,
            'images' => [
                'https://images.unsplash.com/photo-1606767340814-3d0bd9c7d6b7?w=500',
                'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?w=500',
                'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=500',
                'https://images.unsplash.com/photo-1542362567-b07e54358753?w=500',
                'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=500',
                'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=500',
            ],
            'description' => 'Топливный насос Bosch 0 580 464 070 обеспечивает стабильную подачу топлива и надежную работу двигателя. Высокое качество изготовления и соответствие оригинальным спецификациям гарантируют долговечность и эффективность работы топливной системы.',
            'specs' => [
                ['label' => 'Бренд', 'value' => 'Bosch'],
                ['label' => 'Страна производства', 'value' => 'Германия'],
                ['label' => 'Тип', 'value' => 'Электрический топливный насос'],
                ['label' => 'Рабочее напряжение', 'value' => '12 В'],
                ['label' => 'Производительность', 'value' => '110 л/ч'],
                ['label' => 'Давление', 'value' => '3.5 бар'],
            ],
            'compatibility' => [
                ['brand' => 'BMW', 'models' => 'BMW 3 Series (E46), BMW 5 Series (E39), BMW X5 (E53)'],
                ['brand' => 'Audi', 'models' => 'Audi A4 B6, Audi A6 C5, Audi A8 D2'],
                ['brand' => 'Volkswagen', 'models' => 'VW Passat B5, VW Golf 4, VW Touareg'],
            ],
            'reviews' => [
                ['name' => 'Александр', 'initial' => 'А', 'car' => 'BMW 5 серия', 'date' => '15/05/2026', 'text' => 'Топливный насос Bosch 0 580 464 070 обеспечивает стабильную подачу топлива и надежную работу двигателя. Качество отличное, рекомендую.', 'photos' => ['https://images.unsplash.com/photo-1606767340814-3d0bd9c7d6b7?w=200', 'https://images.unsplash.com/photo-1606767340814-3d0bd9c7d6b7?w=200', 'https://images.unsplash.com/photo-1606767340814-3d0bd9c7d6b7?w=200']],
                ['name' => 'Михаил', 'initial' => 'М', 'car' => 'Audi A4', 'date' => '10/05/2026', 'text' => 'Отличный насос, работает исправно уже полгода. Цена приемлемая, доставка быстрая.', 'photos' => ['https://images.unsplash.com/photo-1606767340814-3d0bd9c7d6b7?w=200']],
            ],
        ];

        $format = static fn (int $price) => number_format($price, 0, ',', ' ').' ₽';

        return view('product', [
            'product' => $product,
            'priceFormatted' => $format($product['price']),
            'oldPriceFormatted' => $format($product['oldPrice']),
            'savingsFormatted' => $format($product['savings']),
        ]);
    })->name('product');
};

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');

    return response()->json(['status' => 'ok']);
});

// Default locale (no prefix)
Route::middleware('locale:'.config('app.available_locales.0'))->group($register);

// Non-default locales (/{locale} prefix)
foreach (array_slice(config('app.available_locales'), 1) as $locale) {
    Route::prefix($locale)
        ->name("{$locale}.")
        ->middleware("locale:{$locale}")
        ->group($register);
}
