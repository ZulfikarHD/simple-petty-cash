<?php

use App\Http\Controllers\CashFundController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('transactions', TransactionController::class)
        ->except(['show']);

    Route::delete('transactions/{transaction}/receipt', [TransactionController::class, 'destroyReceipt'])
        ->name('transactions.receipt.destroy');

    Route::get('cash-fund', [CashFundController::class, 'create'])->name('cash-fund.create');
    Route::post('cash-fund', [CashFundController::class, 'store'])->name('cash-fund.store');

    Route::get('api/categories', [CategoryController::class, 'index'])->name('categories.index');
});

require __DIR__.'/settings.php';
