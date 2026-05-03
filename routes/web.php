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

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updateProfile'])->name('profile.password');

    // Master Data
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('brands', BrandController::class)->except(['create', 'show', 'edit']);
    Route::resource('units', UnitController::class)->except(['create', 'show', 'edit']);
    Route::resource('tax-rates', TaxRateController::class)->except(['create', 'show', 'edit']);

    // Products
    Route::resource('products', ProductController::class);
    Route::get('/api/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/api/products/search', [ProductController::class, 'search'])->name('product.search');

    // Contacts
    Route::resource('contacts', ContactController::class)->except(['create', 'show', 'edit']);
    Route::resource('customer-groups', CustomerGroupController::class)->except(['create', 'show', 'edit']);

    // POS & Sales
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/drafts', [SaleController::class, 'drafts'])->name('sales.drafts');
    Route::delete('/sales/drafts/{draft}', [SaleController::class, 'destroy'])->name('sales.drafts.destroy');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');

    // Purchases
    Route::resource('purchases', PurchaseController::class);

    // Stock
    Route::get('/stock/adjustments', [StockController::class, 'adjustments'])->name('stock.adjustments');
    Route::post('/stock/adjustments', [StockController::class, 'storeAdjustment'])->name('stock.adjustments.store');
    Route::get('/stock/adjustments/{id}', [StockController::class, 'adjustments'])->name('stock.adjustments.show');
    Route::get('/stock/transfers', [StockController::class, 'transfers'])->name('stock.transfers');
    Route::post('/stock/transfers', [StockController::class, 'storeTransfer'])->name('stock.transfers.store');
    Route::get('/stock/transfers/{id}', [StockController::class, 'transfers'])->name('stock.transfers.show');

    // Expenses
    Route::resource('expenses', ExpenseController::class)->except(['create', 'show', 'edit']);
    Route::resource('expense-categories', ExpenseCategoryController::class)->except(['create', 'show', 'edit']);

    // Reports
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/purchases', [ReportController::class, 'purchases'])->name('reports.purchases');
    Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
    Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
    Route::get('/reports/tax', [ReportController::class, 'tax'])->name('reports.tax');

    // Cash Register
    Route::get('/cash-register', [CashRegisterController::class, 'index'])->name('cash-register.index');
    Route::post('/cash-register/open', [CashRegisterController::class, 'open'])->name('cash-register.open');
    Route::post('/cash-register/close', [CashRegisterController::class, 'close'])->name('cash-register.close');
    Route::get('/cash-register/{id}', [CashRegisterController::class, 'index'])->name('cash-register.show');

    // Accounts
    Route::resource('accounts', AccountController::class)->except(['create', 'show', 'edit']);
    Route::resource('account-types', AccountTypeController::class)->except(['create', 'show', 'edit']);
    Route::get('/accounts/transactions', [AccountController::class, 'transactions'])->name('accounts.transactions');
    Route::post('/accounts/transactions', [AccountController::class, 'storeTransaction']);

    // Users & Roles
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    Route::resource('roles', RoleController::class)->except(['create', 'show', 'edit']);

    // Settings
    Route::get('/settings/business', [SettingsController::class, 'business'])->name('settings.business');
    Route::put('/settings/business', [SettingsController::class, 'updateBusiness'])->name('settings.business.update');
    Route::get('/settings/locations', [SettingsController::class, 'locations'])->name('settings.locations');
    Route::put('/settings/locations/{location}', [SettingsController::class, 'updateLocation'])->name('settings.locations.update');
    Route::get('/settings/invoice-layouts', [SettingsController::class, 'invoiceLayouts'])->name('settings.invoice-layouts');
    Route::get('/settings/system', [SettingsController::class, 'system'])->name('settings.system');
    Route::put('/settings/system', [SettingsController::class, 'updateSystem'])->name('settings.system.update');
});
