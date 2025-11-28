<?php

use App\Http\Controllers\CashFundController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
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

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Admin User Management
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('users.reset-password');
    });
});

require __DIR__.'/settings.php';
