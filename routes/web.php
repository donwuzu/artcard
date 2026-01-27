<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\SampleImageController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ClockOrderController;
use App\Http\Controllers\PortraitController;
use App\Http\Controllers\PortraitClockController;
use App\Http\Controllers\ClockExpenseController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SampleClockController;

/*
|--------------------------------------------------------------------------
| Models
|--------------------------------------------------------------------------
*/
use App\Models\Portrait;

/*
|--------------------------------------------------------------------------
| Public / Guest Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome', [
        'portraits' => Portrait::latest()->paginate(50),
        'showDiscountBanner' => true,
    ]);
})->name('home');

// Renamed 'clocks.index' -> 'decor.index'
Route::get('/decor', [PortraitClockController::class, 'index'])
    ->name('clocks.index');

Route::get('/sample-portraits', [SampleImageController::class, 'index'])
    ->name('sample-images.index');

// Renamed 'sample-clocks.index' -> 'sample-decor.index'
Route::get('/sample-decor', [SampleClockController::class, 'index'])
    ->name('sample-clocks.index');      
    
Route::get('/cart', [CartController::class, 'show'])
    ->name('cart.index');

Route::post('/cart', [CartController::class, 'store'])
    ->name('cart.store');

Route::post('/checkout', [OrderController::class, 'store'])
    ->name('order.store');

// Renamed 'clocks.order.store' -> 'decor.order.store'
Route::post('/decor-order', [ClockOrderController::class, 'store'])
    ->name('clocks.order.store');


/*
|--------------------------------------------------------------------------
| Auth Redirect (single entry point)
|--------------------------------------------------------------------------
*/
Route::get('/home', function () {
    if (Auth::check() && Auth::user()->hasRole('admin')) {
        return redirect()->route('admin.home');
    }

    if (Auth::check()) {
        return redirect()->route('client.home');
    }

    return redirect()->route('home'); 
});


/*
|--------------------------------------------------------------------------
| Admin Authentication (Guest Only)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])
            ->name('admin.login');

        Route::post('/login', [AdminLoginController::class, 'login'])
            ->name('admin.login.submit');
    });

    Route::post('/logout', [AdminLoginController::class, 'logout'])
        ->middleware('auth')
        ->name('admin.logout');
});


/*
|--------------------------------------------------------------------------
| Client Routes
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

        Route::get('/cart', [CartController::class, 'show'])
            ->name('cart.index');

        Route::post('/cart', [CartController::class, 'store'])
            ->name('cart.store');

        Route::post('/checkout', [OrderController::class, 'store'])
            ->name('order.store');

        // Renamed to match public naming convention
        Route::get('/decor', [PortraitClockController::class, 'index'])
            ->name('clocks.index');

        Route::post('/decor-order', [ClockOrderController::class, 'store'])
            ->name('clocks.order.store');
    });


/*
|--------------------------------------------------------------------------
| Admin Routes (Admin Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/home', function () {
            return view('welcome', [
                'portraits' => Portrait::latest()->paginate(50),
                'showDiscountBanner' => true,
            ]);
        })->name('home');

        /* Dashboard */
        Route::get('/dashboard', [PortraitController::class, 'index'])
            ->name('dashboard');

        /* Portraits */
        Route::resource('portraits', PortraitController::class);

        /* Decor (formerly Clocks) */
        // Manual index route
        Route::get('/decor', [PortraitClockController::class, 'index'])
            ->name('decor.index');

        // Resource for store/update/destroy (generates admin.decor.store, etc.)
        Route::resource('decor', PortraitClockController::class)
            ->except('index');

        Route::get('/decor-dashboard', [PortraitClockController::class, 'dashboard'])
            ->name('decor.dashboard');

        /* Sample Images */
        Route::resource('sample-images', SampleImageController::class)
            ->except('index');

        Route::get('/sample-images-dashboard', [SampleImageController::class, 'dashboard'])
            ->name('sample-images.dashboard');

        /* Sample Decor (formerly Sample Clocks) */
        // Updated URI to 'sample-decor'
        Route::resource('sample-decor', SampleClockController::class)
            ->only(['store', 'destroy'])
            ->names([
                'store' => 'sample-clocks.store',
                'destroy' => 'sample-clocks.destroy',
            ]);

        Route::get('/sample-decor/dashboard', [SampleClockController::class, 'dashboard'])
             ->name('sample-clocks.dashboard');


        /* Expenses */
        Route::resource('expenses', ExpenseController::class)
            ->only(['index', 'destroy']);

        Route::post('/expenses/{order}/toggle', [ExpenseController::class, 'toggleStatus'])
            ->name('expenses.toggle');

        Route::get('/reports', [ExpenseController::class, 'report'])
            ->name('expenses.report');

        /* Decor Expenses (formerly Clock Expenses) */
        // Updated names to be consistent
        Route::get('/decor-expenses', [ClockExpenseController::class, 'index'])
            ->name('clock-expenses.index');

        Route::delete('/decor-expenses/{clockOrder}', [ClockExpenseController::class, 'destroy'])
            ->name('clock-expenses.destroy');

        Route::get('/decor-reports', [ClockExpenseController::class, 'report'])
            ->name('clock-expenses.report');   

        /* Users */
        Route::resource('users', UserController::class)
            ->only(['index', 'update', 'destroy']);

        Route::patch('users/{user}/role', [UserController::class, 'updateRole'])
            ->name('users.update-role');

        /* Profile */
        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::patch('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::delete('/profile', [ProfileController::class, 'destroy'])
            ->name('profile.destroy');
    });

/*
|--------------------------------------------------------------------------
| Auth Scaffolding
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';