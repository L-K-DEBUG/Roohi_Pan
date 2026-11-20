<?php

use App\Http\Controllers\CreditController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('pos.index')
        : redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('pos.index'))->name('dashboard');
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/orders', [PosController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}/receipt', [PosController::class, 'receipt'])->name('orders.receipt');
    Route::post('/orders/{order}/cancel', [PosController::class, 'cancel'])->name('orders.cancel');

    Route::get('/credit', [CreditController::class, 'index'])->name('credit.index');
    Route::post('/credit/{order}/settle', [CreditController::class, 'settle'])->name('credit.settle');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::patch('/inventory/{inventory}', [InventoryController::class, 'adjust'])->name('inventory.adjust');

    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::patch('/staff/{user}/promote', [StaffController::class, 'promote'])->name('staff.promote');
    Route::delete('/staff/{user}', [StaffController::class, 'destroy'])->name('staff.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
