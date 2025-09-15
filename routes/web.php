<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PortraitController;
use App\Http\Controllers\PortraitClockController;
use App\Http\Controllers\ClockExpenseController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Admin\AdminDashboardController;

use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

use App\Models\Portrait;

use App\Models\PortraitClock;


use App\Http\Controllers\OrderController;

use App\Http\Controllers\ClockOrderController;

use App\Http\Controllers\Auth\AdminLoginController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'role:client'])->group(function () {
   Route::get('/home', function () {
        $portraits = Portrait::latest()->paginate(50);
        return view('welcome', [
            'portraits' => $portraits,
            'showDiscountBanner' => true
        ]);
    })->name('home');

Route::post('/checkout', action: [OrderController::class, 'store'])->name('order.store');



Route::get('/cart', [CartController::class, 'show'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');




Route::get('/clocks', [PortraitClockController::class, 'index'])->name('clocks.index');

Route::post('/clock-order', [ClockOrderController::class, 'store'])->name('clock.order.store');

});


// ✅ Admin login routes
Route::middleware('guest')->group(function () {
    Route::get('admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
});
Route::post('admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');


// Dashboard route with portrait list/upload


// Admin Authenticated routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
   

    Route::get('/dashboard', [PortraitController::class, 'index'])->name('admin.dashboard');
    // Portrait upload POST route
  Route::resource('portraits', PortraitController::class);


    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses/{order}/toggle', [ExpenseController::class, 'toggleStatus'])->name('expenses.toggleStatus');

    Route::get('/reports', [ExpenseController::class, 'report'])->name('expenses.report');
    Route::post('/reports/{order}/toggle', [ExpenseController::class, 'toggleStatusFromReport'])->name('expenses.toggleFromReport');

    Route::delete('/expenses/{order}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');



     Route::resource('clocks', PortraitClockController::class)->except(['index']);
    Route::get('/clocks-dashboard', [PortraitClockController::class, 'dashboard'])->name('clocks.dashboard');


    Route::get('/clock-expenses', [ClockExpenseController::class, 'index'])->name('clockExpenses.index');

    Route::post('/clock-expenses/{clockOrder}/toggle', [ClockExpenseController::class, 'toggleStatus'])->name('clockExpenses.toggleStatus');

    Route::get('/clock-reports', [ClockExpenseController::class, 'report'])->name('clockExpenses.report');

    Route::post('/clock-reports/{clockOrder}/toggle', [ClockExpenseController::class, 'toggleStatusFromReport'])->name('clockExpenses.toggleFromReport');

    Route::delete('/clock-expenses/{clockOrder}', [ClockExpenseController::class, 'destroy'])->name('clockExpenses.destroy');


    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});







// Breeze auth scaffolding
require __DIR__.'/auth.php';
