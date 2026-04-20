<?php

use App\Http\Controllers\AccountingDashboardController;
use App\Http\Controllers\AccountantTransactionController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdminPettyCashController;
use App\Http\Controllers\PettyCashExpenseController;
use App\Http\Controllers\PosDashboardController;
use App\Http\Controllers\PosProductController;
use App\Http\Controllers\PosSaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if (auth()->user()->role === 'salesperson') {
        return redirect()->route('pos.dashboard');
    }

    return redirect()->route('accounting.dashboard');
})->middleware(['auth', 'verified', 'active'])->name('dashboard');

Route::middleware(['auth', 'verified', 'active', 'audit.entry'])->group(function () {
    Route::get('/reports/transactions', [ReportController::class, 'transactions'])->name('reports.transactions');
    Route::get('/reports/transactions/pdf', [ReportController::class, 'transactionsPdf'])->name('reports.transactions.pdf');

    // Admin + Accountant: basic operational reports (sales, suppliers)
    Route::middleware('role:admin,accountant')->group(function () {
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/sales/pdf', [ReportController::class, 'salesPdf'])->name('reports.sales.pdf');
        Route::get('/reports/suppliers-aging', [ReportController::class, 'suppliersAging'])->name('reports.suppliers-aging');
        Route::get('/reports/suppliers-aging/pdf', [ReportController::class, 'suppliersAgingPdf'])->name('reports.suppliers-aging.pdf');
        Route::post('/reports/suppliers-aging/{transaction}/payments', [ReportController::class, 'recordSupplierPayment'])->name('reports.suppliers-aging.payments');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/hub', [ReportController::class, 'index'])->name('reports.hub');
        Route::get('/reports/income-statement', [ReportController::class, 'incomeStatement'])->name('reports.income-statement');
        Route::get('/reports/income-statement/pdf', [ReportController::class, 'incomeStatementPdf'])->name('reports.income-statement.pdf');
        Route::get('/reports/balance-sheet', [ReportController::class, 'balanceSheet'])->name('reports.balance-sheet');
        Route::get('/reports/balance-sheet/pdf', [ReportController::class, 'balanceSheetPdf'])->name('reports.balance-sheet.pdf');
        Route::get('/reports/trial-balance', [ReportController::class, 'trialBalance'])->name('reports.trial-balance');
        Route::get('/reports/trial-balance/pdf', [ReportController::class, 'trialBalancePdf'])->name('reports.trial-balance.pdf');
        Route::get('/reports/reconciliation', [ReportController::class, 'reconciliation'])->name('reports.reconciliation');
        Route::post('/reports/reconciliation/reconcile', [ReportController::class, 'reconcileTransactions'])->name('reports.reconciliation.reconcile');
        Route::get('/reports/reconciliation/pdf', [ReportController::class, 'reconciliationPdf'])->name('reports.reconciliation.pdf');
    });

    // Accountant only: accounting dashboard, transaction CRUD, COA, settings
    Route::middleware('role:accountant')->group(function () {
        Route::get('/accounting/dashboard', [AccountingDashboardController::class, 'index'])->name('accounting.dashboard');
        Route::get('/accounting/transactions', fn () => redirect()->route('reports.transactions'))->name('accounting.transactions.index');
        Route::get('/accounting/transactions/create', [AccountantTransactionController::class, 'create'])->name('accounting.transactions.create');
        Route::post('/accounting/transactions', [AccountantTransactionController::class, 'store'])->name('accounting.transactions.store');
        Route::get('/accounting/transactions/{transaction}/edit', [AccountantTransactionController::class, 'edit'])->name('accounting.transactions.edit');
        Route::put('/accounting/transactions/{transaction}', [AccountantTransactionController::class, 'update'])->name('accounting.transactions.update');
        Route::delete('/accounting/transactions/{transaction}', [AccountantTransactionController::class, 'destroy'])->name('accounting.transactions.destroy');
        Route::post('/accounting/transactions/import', [AccountantTransactionController::class, 'import'])->name('accounting.transactions.import');
        Route::get('/accounting/transactions/import/template', [AccountantTransactionController::class, 'template'])->name('accounting.transactions.template');
        Route::get('/admin/accounts', [AccountController::class, 'index'])->name('admin.accounts.index');
        Route::get('/admin/accounts/create', [AccountController::class, 'create'])->name('admin.accounts.create');
        Route::post('/admin/accounts', [AccountController::class, 'store'])->name('admin.accounts.store');
        Route::get('/admin/accounts/{account}/edit', [AccountController::class, 'edit'])->name('admin.accounts.edit');
        Route::put('/admin/accounts/{account}', [AccountController::class, 'update'])->name('admin.accounts.update');
        Route::delete('/admin/accounts/{account}', [AccountController::class, 'destroy'])->name('admin.accounts.destroy');
        Route::patch('/admin/accounts/{account}/toggle', [AccountController::class, 'toggle'])->name('admin.accounts.toggle');
        Route::get('/accounting/settings', [AdminSettingsController::class, 'accountantSettings'])->name('accounting.settings');
        Route::patch('/accounting/settings/posting-rules', [AdminSettingsController::class, 'updatePostingRules'])->name('admin.settings.posting-rules');
        Route::get('/accounting/settings/activity/pdf', [AdminSettingsController::class, 'activityPdf'])->name('admin.settings.activity.pdf');
    });

    // Admin: purchasing, stock control, inventory, users, petty cash
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
        Route::resource('/admin/categories', CategoryController::class)->except('show')->names('admin.categories');
        Route::resource('/admin/suppliers', SupplierController::class)->except('show')->names('admin.suppliers');
        Route::patch('/admin/suppliers/{supplier}/toggle', [SupplierController::class, 'toggle'])->name('admin.suppliers.toggle');
        Route::post('/admin/suppliers/import', [SupplierController::class, 'import'])->name('admin.suppliers.import');
        Route::get('/admin/suppliers/import/template', [SupplierController::class, 'template'])->name('admin.suppliers.template');
        Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
        Route::patch('/admin/users/{user}/deactivate', [UserManagementController::class, 'deactivate'])->name('admin.users.deactivate');
        Route::get('/admin/pos-products', [PosProductController::class, 'index'])->name('admin.pos-products.index');
        Route::get('/admin/pos-products/create', [PosProductController::class, 'create'])->name('admin.pos-products.create');
        Route::post('/admin/pos-products', [PosProductController::class, 'store'])->name('admin.pos-products.store');
        Route::get('/admin/pos-products/{posProduct}/edit', [PosProductController::class, 'edit'])->name('admin.pos-products.edit');
        Route::put('/admin/pos-products/{posProduct}', [PosProductController::class, 'update'])->name('admin.pos-products.update');
        Route::delete('/admin/pos-products/{posProduct}', [PosProductController::class, 'destroy'])->name('admin.pos-products.destroy');
        Route::patch('/admin/pos-products/{posProduct}/toggle', [PosProductController::class, 'toggle'])->name('admin.pos-products.toggle');
        Route::get('/admin/petty-cash', [AdminPettyCashController::class, 'index'])->name('admin.petty-cash.index');
        Route::post('/admin/petty-cash/allocate', [AdminPettyCashController::class, 'allocate'])->name('admin.petty-cash.allocate');
        Route::get('/admin/petty-cash/{user}/report', [AdminPettyCashController::class, 'report'])->name('admin.petty-cash.report');
    });

    Route::middleware('role:salesperson')->group(function () {
        Route::get('/pos/dashboard', [PosDashboardController::class, 'index'])->name('pos.dashboard');
        Route::get('/pos/sell', [PosSaleController::class, 'create'])->name('pos.sell');
        Route::post('/pos/sales', [PosSaleController::class, 'store'])->name('pos.sales.store');
        Route::get('/pos/sales', [PosSaleController::class, 'index'])->name('pos.sales.index');
        Route::get('/pos/sales/{posSale}/receipt', [PosSaleController::class, 'receipt'])->name('pos.receipt');
        Route::get('/pos/petty-cash', [PettyCashExpenseController::class, 'index'])->name('pos.petty-cash.index');
        Route::post('/pos/petty-cash', [PettyCashExpenseController::class, 'store'])->name('pos.petty-cash.store');
        Route::delete('/pos/petty-cash/{expense}', [PettyCashExpenseController::class, 'destroy'])->name('pos.petty-cash.destroy');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
