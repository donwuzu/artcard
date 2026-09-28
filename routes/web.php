<?php

use App\Models\Portrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (STORE FRONT)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome', [
        'portraits' => Portrait::latest()->paginate(50),
        'showDiscountBanner' => true,
    ]);
})->name('home');

/*
|--------------------------------------------------------------------------
| DECOR ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/decor', [App\Http\Controllers\DecorController::class, 'index'])
    ->name('decor.index');

Route::get('/sample-portraits', [App\Http\Controllers\SampleImageController::class, 'index'])
    ->name('sample-images.index');

Route::get('/sample-decor', [App\Http\Controllers\SampleDecorController::class, 'index'])
    ->name('sample-decor.index');

/*
|--------------------------------------------------------------------------
| CART & ORDERS
|--------------------------------------------------------------------------
*/

Route::get('/cart', [App\Http\Controllers\CartController::class, 'show'])->name('cart.index');
Route::post('/cart', [App\Http\Controllers\CartController::class, 'store'])->name('cart.store');
Route::post('/checkout', [App\Http\Controllers\CartController::class, 'store'])->name('order.store');

Route::post('/decor-order', [App\Http\Controllers\CartController::class, 'store'])
    ->name('decor.order.store');

/*
|--------------------------------------------------------------------------
| CLIENT ROUTES (OPTIONAL FUTURE PANEL)
|--------------------------------------------------------------------------
*/

Route::prefix('client')
    ->name('client.')
    ->group(function () {

        Route::get('/home', function () {
            return view('welcome', [
                'portraits' => Portrait::latest()->paginate(50),
                'showDiscountBanner' => true,
            ]);
        })->name('home');

        Route::get('/cart', [App\Http\Controllers\CartController::class, 'show'])->name('cart.index');
        Route::post('/cart', [App\Http\Controllers\CartController::class, 'store'])->name('cart.store');
        Route::post('/checkout', [App\Http\Controllers\CartController::class, 'store'])->name('order.store');
    });

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
