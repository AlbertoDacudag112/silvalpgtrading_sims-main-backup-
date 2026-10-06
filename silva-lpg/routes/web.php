<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\OrderController as AdminOrder;
use App\Http\Controllers\Admin\InventoryController as AdminInventory;
use App\Http\Controllers\Admin\ExpenseController as AdminExpense;
use App\Http\Controllers\Admin\ReportController as AdminReport;
use App\Http\Controllers\Admin\DeliveryStaffController as AdminDeliveryStaff;
use App\Http\Controllers\Cashier\DashboardController as CashierDashboard;
use App\Http\Controllers\Cashier\OrderController as CashierOrder;
use App\Http\Controllers\Cashier\InventoryController as CashierInventory;
use App\Http\Controllers\Cashier\ExpenseController as CashierExpense;
use App\Http\Controllers\Delivery\DashboardController as DeliveryDashboard;
use App\Http\Controllers\Delivery\OrderController as DeliveryOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route(Auth::user()->homeRoute())
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return redirect()->route(Auth::user()->homeRoute());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::get('/orders', [AdminOrder::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [AdminOrder::class, 'create'])->name('orders.create');
    Route::post('/orders', [AdminOrder::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [AdminOrder::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/assign', [AdminOrder::class, 'assign'])->name('orders.assign');
    Route::post('/orders/{order}/cancel', [AdminOrder::class, 'cancel'])->name('orders.cancel');

    Route::get('/inventory', [AdminInventory::class, 'index'])->name('inventory.index');
    Route::get('/inventory/create', [AdminInventory::class, 'create'])->name('inventory.create');
    Route::post('/inventory', [AdminInventory::class, 'store'])->name('inventory.store');
    Route::post('/inventory/{product}/restock', [AdminInventory::class, 'restock'])->name('inventory.restock');
    Route::get('/inventory/{product}/logs', [AdminInventory::class, 'logs'])->name('inventory.logs');

    Route::get('/expenses', [AdminExpense::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [AdminExpense::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/{expense}', [AdminExpense::class, 'destroy'])->name('expenses.destroy');

    Route::get('/reports', [AdminReport::class, 'index'])->name('reports.index');

    // Delivery staff are non-permanent and not created via public registration —
    // the Owner is the only one who can create, edit, or remove their accounts.
    Route::get('/delivery-staff', [AdminDeliveryStaff::class, 'index'])->name('delivery-staff.index');
    Route::get('/delivery-staff/create', [AdminDeliveryStaff::class, 'create'])->name('delivery-staff.create');
    Route::post('/delivery-staff', [AdminDeliveryStaff::class, 'store'])->name('delivery-staff.store');
    Route::get('/delivery-staff/{deliveryStaff}/edit', [AdminDeliveryStaff::class, 'edit'])->name('delivery-staff.edit');
    Route::put('/delivery-staff/{deliveryStaff}', [AdminDeliveryStaff::class, 'update'])->name('delivery-staff.update');
    Route::delete('/delivery-staff/{deliveryStaff}', [AdminDeliveryStaff::class, 'destroy'])->name('delivery-staff.destroy');
});

Route::middleware(['auth', 'verified', 'role:cashier'])->prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/dashboard', [CashierDashboard::class, 'index'])->name('dashboard');

    Route::get('/orders', [CashierOrder::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [CashierOrder::class, 'create'])->name('orders.create');
    Route::post('/orders', [CashierOrder::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [CashierOrder::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/assign', [CashierOrder::class, 'assign'])->name('orders.assign');
    Route::post('/orders/{order}/cancel', [CashierOrder::class, 'cancel'])->name('orders.cancel');

    Route::get('/inventory', [CashierInventory::class, 'index'])->name('inventory.index');
    Route::post('/inventory/{product}/restock', [CashierInventory::class, 'restock'])->name('inventory.restock');

    Route::get('/expenses', [CashierExpense::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [CashierExpense::class, 'store'])->name('expenses.store');
});

Route::middleware(['auth', 'verified', 'role:delivery'])->prefix('delivery')->name('delivery.')->group(function () {
    Route::get('/dashboard', [DeliveryDashboard::class, 'index'])->name('dashboard');
    Route::get('/orders', [DeliveryOrder::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [DeliveryOrder::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/complete', [DeliveryOrder::class, 'complete'])->name('orders.complete');
});

require __DIR__ . '/auth.php';
