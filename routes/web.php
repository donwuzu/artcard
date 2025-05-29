<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PortraitController;
use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

use App\Models\Portrait;

use App\Http\Controllers\OrderController;

Route::get('/', function () {
    $portraits = Portrait::latest()->paginate(5); // Show 50 items per page
    return view('welcome', [
        'portraits' => $portraits,
        'showDiscountBanner' => true
    ]);
})->name('home');


// Dashboard route with portrait list/upload
Route::get('/dashboard', [PortraitController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Portrait upload POST route
  Route::resource('portraits', PortraitController::class);


    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses/{order}/toggle', [ExpenseController::class, 'toggleStatus'])->name('expenses.toggleStatus');

    Route::get('/reports', [ExpenseController::class, 'report'])->name('expenses.report');
    Route::post('/reports/{order}/toggle', [ExpenseController::class, 'toggleStatusFromReport'])->name('expenses.toggleFromReport');

    Route::delete('/expenses/{order}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');


    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::post('/checkout', [OrderController::class, 'store'])->name('order.store');




// Breeze auth scaffolding
require __DIR__.'/auth.php';
