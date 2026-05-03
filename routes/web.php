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
    Route::post('/pos/restore/{id}', [PosController::class, 'store'])->name('pos.restore');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/drafts', [SaleController::class, 'drafts'])->name('sales.drafts');
    Route::delete('/sales/drafts/{draft}', [SaleController::class, 'destroy'])->name('sale.drafts.destroy');
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

    // Report aliases (views use underscores)
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('report.sales');
    Route::get('/reports/purchases', [ReportController::class, 'purchases'])->name('report.purchases');
    Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('report.profit_loss');
    Route::get('/reports/tax', [ReportController::class, 'tax'])->name('report.tax');

    // Cash Register
    Route::get('/cash-register', [CashRegisterController::class, 'index'])->name('cash-register.index');
    Route::post('/cash-register/open', [CashRegisterController::class, 'open'])->name('cash-register.open');
    Route::post('/cash-register/close/{id}', [CashRegisterController::class, 'close'])->name('cash-register.close');
    Route::get('/cash-register/{id}', [CashRegisterController::class, 'index'])->name('cash-register.show');
    // Aliases with underscores
    Route::post('/cash-register/open', [CashRegisterController::class, 'open'])->name('cash_register.open');
    Route::post('/cash-register/close/{id}', [CashRegisterController::class, 'close'])->name('cash_register.close');
    Route::get('/cash-register/{id}', [CashRegisterController::class, 'index'])->name('cash_register.show');

    // Accounts (using custom name prefix to match view conventions)
    Route::get('/accounts', [AccountController::class, 'index'])->name('account.index');
    Route::post('/accounts', [AccountController::class, 'store'])->name('account.store');
    Route::put('/accounts/{account}', [AccountController::class, 'update'])->name('account.update');
    Route::delete('/accounts/{account}', [AccountController::class, 'destroy'])->name('account.destroy');

    // Account Types
    Route::get('/account-types', [AccountTypeController::class, 'index'])->name('account_type.index');
    Route::post('/account-types', [AccountTypeController::class, 'store'])->name('account_type.store');
    Route::put('/account-types/{account_type}', [AccountTypeController::class, 'update'])->name('account_type.update');
    Route::delete('/account-types/{account_type}', [AccountTypeController::class, 'destroy'])->name('account_type.destroy');

    // Account Transactions
    Route::get('/account/transactions', [AccountController::class, 'transactions'])->name('account.transactions.index');
    Route::post('/account/transactions', [AccountController::class, 'storeTransaction'])->name('account.transactions.store');
    Route::put('/account/transactions/{id}', [AccountController::class, 'storeTransaction'])->name('account.transactions.update');
    Route::delete('/account/transactions/{id}', [AccountController::class, 'storeTransaction'])->name('account.transactions.destroy');

    // Users & Roles (using custom name prefix to match view conventions)
    Route::get('/users', [UserController::class, 'index'])->name('manage_user.index');
    Route::post('/users', [UserController::class, 'store'])->name('manage_user.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('manage_user.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('manage_user.destroy');

    Route::get('/roles', [RoleController::class, 'index'])->name('role.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('role.store');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('role.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('role.destroy');

    // Settings
    Route::get('/settings/business', [SettingsController::class, 'business'])->name('settings.business');
    Route::put('/settings/business', [SettingsController::class, 'updateBusiness'])->name('settings.business.update');
    Route::get('/settings/locations', [SettingsController::class, 'locations'])->name('settings.locations');
    Route::post('/settings/locations', [SettingsController::class, 'storeLocation'])->name('settings.locations.store');
    Route::put('/settings/locations/{location}', [SettingsController::class, 'updateLocation'])->name('settings.locations.update');
    Route::get('/settings/invoice-layouts', [SettingsController::class, 'invoiceLayouts'])->name('settings.invoice-layouts');
    Route::get('/settings/system', [SettingsController::class, 'system'])->name('settings.system');
    Route::put('/settings/system', [SettingsController::class, 'updateSystem'])->name('settings.system.update');

    // Sidebar aliases
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/account-types', [AccountTypeController::class, 'index'])->name('account-types.index');
    Route::get('/account/transactions', [AccountController::class, 'transactions'])->name('accounts.transactions');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
});
