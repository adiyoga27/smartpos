<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerGroupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Auth required routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('permission:dashboard.view')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index']);
    });

    // Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updateProfile'])->name('profile.password');

    // Master Data
    Route::middleware('permission:categories.view')->resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
    Route::middleware('permission:brands.view')->resource('brands', BrandController::class)->except(['create', 'show', 'edit']);
    Route::middleware('permission:units.view')->resource('units', UnitController::class)->except(['create', 'show', 'edit']);
    Route::middleware('permission:tax-rates.view')->resource('tax-rates', TaxRateController::class)->except(['create', 'show', 'edit']);

    // Products
    Route::middleware('permission:products.view')->resource('products', ProductController::class);
    Route::get('/api/products/search', [ProductController::class, 'search'])->name('product.search');

    // Contacts
    Route::middleware('permission:contacts.view')->resource('contacts', ContactController::class)->except(['create', 'show', 'edit']);
    Route::get('/api/customers/search', [ContactController::class, 'search'])->name('customers.search');
    Route::middleware('permission:customer-groups.view')->resource('customer-groups', CustomerGroupController::class)->except(['create', 'show', 'edit']);

    // POS & Sales
    Route::middleware('permission:pos.view')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
        Route::post('/pos/set-location', [PosController::class, 'setLocation'])->name('pos.set-location');
        Route::get('/pos/{transaction}/print/thermal', [PosController::class, 'printThermal'])->name('pos.print.thermal');
        Route::get('/pos/{transaction}/print/a4', [PosController::class, 'printA4'])->name('pos.print.a4');
    });
    Route::middleware('permission:sales.view')->group(function () {
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/drafts', [SaleController::class, 'drafts'])->name('sales.drafts');
        Route::delete('/sales/drafts/{draft}', [SaleController::class, 'destroy'])->name('sales.drafts.destroy');
        Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
        Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
    });

    // Purchases
    Route::middleware('permission:purchases.view')->resource('purchases', PurchaseController::class);
    Route::post('/purchases/{purchase}/payment', [PurchaseController::class, 'addPayment'])->name('purchases.payment.store');
    Route::post('/purchases/quick-add-product', [PurchaseController::class, 'quickAddProduct'])->name('purchases.quick-add');

    // Stock
    Route::middleware('permission:stock.view')->group(function () {
        Route::get('/stock/adjustments', [StockController::class, 'adjustments'])->name('stock.adjustments.index');
        Route::post('/stock/adjustments', [StockController::class, 'storeAdjustment'])->name('stock.adjustments.store');
        Route::get('/stock/adjustments/{id}', [StockController::class, 'showAdjustment'])->name('stock.adjustments.show');
        Route::get('/stock/transfers', [StockController::class, 'transfers'])->name('stock.transfers.index');
        Route::post('/stock/transfers', [StockController::class, 'storeTransfer'])->name('stock.transfers.store');
        Route::get('/stock/transfers/{id}', [StockController::class, 'transfers'])->name('stock.transfers.show');
        Route::get('/stock/history', [StockController::class, 'productHistory'])->name('stock.history');
    });

    // Expenses
    Route::middleware('permission:expenses.view')->resource('expenses', ExpenseController::class)->except(['create', 'show', 'edit']);
    Route::middleware('permission:expense-categories.view')->resource('expense-categories', ExpenseCategoryController::class)->except(['create', 'show', 'edit']);

    // Reports
    Route::middleware('permission:reports.view')->group(function () {
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/purchases', [ReportController::class, 'purchases'])->name('reports.purchases');
        Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
        Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
        Route::get('/reports/tax', [ReportController::class, 'tax'])->name('reports.tax');
    });

    // Cash Register
    Route::middleware('permission:cash-register.view')->group(function () {
        Route::get('/cash-register', [CashRegisterController::class, 'index'])->name('cash-register.index');
        Route::post('/cash-register/open', [CashRegisterController::class, 'open'])->name('cash-register.open');
        Route::post('/cash-register/close', [CashRegisterController::class, 'close'])->name('cash-register.close');
        Route::get('/cash-register/{id}', [CashRegisterController::class, 'index'])->name('cash-register.show');
    });

    // Accounts
    Route::middleware('permission:accounts.view')->resource('accounts', AccountController::class)->except(['create', 'show', 'edit']);
    Route::middleware('permission:account-types.view')->resource('account-types', AccountTypeController::class)->except(['create', 'show', 'edit']);
    Route::middleware('permission:accounts.view')->group(function () {
        Route::get('/accounts/transactions', [AccountController::class, 'transactions'])->name('accounts.transactions.index');
        Route::post('/accounts/transactions', [AccountController::class, 'storeTransaction'])->name('accounts.transactions.store');
        Route::put('/accounts/transactions/{transaction}', [AccountController::class, 'updateTransaction'])->name('accounts.transactions.update');
        Route::delete('/accounts/transactions/{transaction}', [AccountController::class, 'destroyTransaction'])->name('accounts.transactions.destroy');
    });

    // Users & Roles
    Route::middleware('permission:users.view')->resource('users', UserController::class)->except(['create', 'show', 'edit']);
    Route::middleware('permission:roles.view')->resource('roles', RoleController::class)->except(['create', 'show', 'edit']);

    // Settings
    Route::middleware('permission:settings.view')->group(function () {
        Route::get('/settings/business', [SettingsController::class, 'business'])->name('settings.business');
        Route::put('/settings/business', [SettingsController::class, 'updateBusiness'])->name('settings.business.update');
        Route::get('/settings/locations', [SettingsController::class, 'locations'])->name('settings.locations');
        Route::post('/settings/locations', [SettingsController::class, 'storeLocation'])->name('settings.locations.store');
        Route::put('/settings/locations/{location}', [SettingsController::class, 'updateLocation'])->name('settings.locations.update');
        Route::get('/settings/invoice-layouts', [SettingsController::class, 'invoiceLayouts'])->name('settings.invoice-layouts');
        Route::post('/settings/invoice-layouts', [SettingsController::class, 'storeInvoiceLayout'])->name('settings.invoice-layouts.store');
        Route::put('/settings/invoice-layouts/{layout}', [SettingsController::class, 'updateInvoiceLayout'])->name('settings.invoice-layouts.update');
        Route::delete('/settings/invoice-layouts/{layout}', [SettingsController::class, 'destroyInvoiceLayout'])->name('settings.invoice-layouts.destroy');
        Route::post('/settings/invoice-schemes', [SettingsController::class, 'storeInvoiceScheme'])->name('settings.invoice-schemes.store');
        Route::put('/settings/invoice-schemes/{scheme}', [SettingsController::class, 'updateInvoiceScheme'])->name('settings.invoice-schemes.update');
        Route::delete('/settings/invoice-schemes/{scheme}', [SettingsController::class, 'destroyInvoiceScheme'])->name('settings.invoice-schemes.destroy');
        Route::get('/settings/system', [SettingsController::class, 'system'])->name('settings.system');
        Route::put('/settings/system', [SettingsController::class, 'updateSystem'])->name('settings.system.update');
    });
});
