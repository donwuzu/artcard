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



    Route::get('/clocks', [PortraitClockController::class, 'index'])
            ->name('clocks.index');


    Route::get('/sample-portraits', [SampleImageController::class, 'index'])
    ->name('sample-images.index');

    Route::get('/sample-clocks', [SampleClockController::class, 'index'])
    ->name('sample-clocks.index');       
    
    
     Route::get('/cart', [CartController::class, 'show'])
            ->name('cart.index');

    Route::post('/cart', [CartController::class, 'store'])
            ->name('cart.store');

    Route::post('/checkout', [OrderController::class, 'store'])
            ->name('order.store');


     Route::post('/clock-order', [ClockOrderController::class, 'store'])
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

    return redirect()->route('home'); // /
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
| Client Routes (Clients + Admins)
|--------------------------------------------------------------------------
| Admins MUST have full access — included intentionally
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

        Route::get('/clocks', [PortraitClockController::class, 'index'])
            ->name('clocks.index');

        Route::post('/clock-order', [ClockOrderController::class, 'store'])
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

        /* Clocks */
        Route::get('/clocks', [PortraitClockController::class, 'index'])
            ->name('clocks.index');

        Route::resource('clocks', PortraitClockController::class)
            ->except('index');

        Route::get('/clocks-dashboard', [PortraitClockController::class, 'dashboard'])
            ->name('clocks.dashboard');

        /* Sample Images */
        Route::resource('sample-images', SampleImageController::class)
            ->except('index');

        Route::get('/sample-images-dashboard', [SampleImageController::class, 'dashboard'])
            ->name('sample-images.dashboard');

           // Resource handles store + destroy
         Route::resource('sample-clocks', SampleClockController::class)
            ->only(['store', 'destroy']);

         // Dashboard (custom, because it's not a resource method)
          Route::get('sample-clocks/dashboard', [SampleClockController::class, 'dashboard'])
               ->name('sample-clocks.dashboard');


        /* Expenses */
        Route::resource('expenses', ExpenseController::class)
            ->only(['index', 'destroy']);

        Route::post('/expenses/{order}/toggle', [ExpenseController::class, 'toggleStatus'])
            ->name('expenses.toggle');

        Route::get('/reports', [ExpenseController::class, 'report'])
            ->name('expenses.report');

        /* Clock Expenses */
        Route::get('/clock-expenses', [ClockExpenseController::class, 'index'])
            ->name('clockExpenses.index');

        Route::delete('/clock-expenses/{clockOrder}', [ClockExpenseController::class, 'destroy'])
            ->name('clockExpenses.destroy');

          Route::get('/clock-reports', [ClockExpenseController::class, 'report'])
            ->name('clockExpenses.report');   

        /* Users */
        Route::resource('users', UserController::class)
            ->only(['index', 'update', 'destroy']);

              // Custom route for updating user role (separate from general update)
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
| Auth Scaffolding (Breeze / Fortify)
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
