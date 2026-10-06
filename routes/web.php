<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoalContributionController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RecurringController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'home')->name('home');
Route::view('/features', 'features')->name('features');
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Registration — 5 per minute per IP
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:5,1');

    // Login — 10 per minute per IP (LoginRequest also has its own 5-attempt lockout)
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:10,1');

    // Password reset — expensive endpoint, tighter limit
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:3,1');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('throttle:5,1');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/income', [ReportController::class, 'income'])->name('income');
        Route::get('/expense', [ReportController::class, 'expense'])->name('expense');

        // Exports — throttled to prevent abuse
        Route::get('/income/export/{format}', [ReportController::class, 'exportIncome'])
            ->middleware('throttle:10,1')->name('income.export');
        Route::get('/expense/export/{format}', [ReportController::class, 'exportExpense'])
            ->middleware('throttle:10,1')->name('expense.export');

        Route::get('/monthly',  [ReportController::class, 'monthly'])->name('monthly');
        Route::get('/yearly',   [ReportController::class, 'yearly'])->name('yearly');
        Route::get('/category', [ReportController::class, 'category'])->name('category');
        Route::get('/budget',   [ReportController::class, 'budget'])->name('budget');
        Route::get('/savings',  [ReportController::class, 'savings'])->name('savings');
        Route::get('/summary',  [ReportController::class, 'summary'])->name('summary');
    });

    // Income
    Route::resource('income', IncomeController::class)->except(['show']);

    // Recurring Transactions
    Route::get('/recurring/run', fn () => redirect()->route('recurring.index'))
         ->name('recurring.run.redirect'); // safety
    Route::post('/recurring/run', [RecurringController::class, 'runNow'])->name('recurring.run');
    Route::resource('recurring', RecurringController::class)
         ->parameters(['recurring' => 'recurring'])
         ->except(['show']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/scan', [NotificationController::class, 'scanNow'])->name('notifications.scan');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Goals
    Route::resource('goals', GoalController::class);
    Route::post('/goals/{goal}/contributions',
        [GoalContributionController::class, 'store']
    )->name('goals.contributions.store');
    Route::delete('/goals/{goal}/contributions/{contribution}',
        [GoalContributionController::class, 'destroy']
    )->name('goals.contributions.destroy');

    // Budgets
    Route::resource('budgets', BudgetController::class)->except(['show']);

    // Expenses
    Route::resource('expenses', ExpenseController::class)->except(['show']);

    // Transactions (unified history)
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Categories
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Payment Methods
    Route::resource('payment-methods', PaymentMethodController::class)
         ->parameters(['payment-methods' => 'payment_method'])
         ->except(['show']);

    // Profile
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile',  [ProfileController::class, 'update'])->name('profile.update');

    // Settings
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])
        ->name('dashboard');

    // Users
    Route::get('/users', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])
        ->name('users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'show'])
        ->name('users.show');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'update'])
        ->name('users.update');
    Route::patch('/users/{user}/toggle-active', [\App\Http\Controllers\Admin\AdminUserController::class, 'toggleActive'])
        ->name('users.toggle-active');

    // Default (system) Categories
    Route::get('/categories', [\App\Http\Controllers\Admin\AdminCategoryController::class, 'index'])
        ->name('categories.index');
    Route::get('/categories/create', [\App\Http\Controllers\Admin\AdminCategoryController::class, 'create'])
        ->name('categories.create');
    Route::post('/categories', [\App\Http\Controllers\Admin\AdminCategoryController::class, 'store'])
        ->name('categories.store');
    Route::get('/categories/{category}/edit', [\App\Http\Controllers\Admin\AdminCategoryController::class, 'edit'])
        ->name('categories.edit');
    Route::put('/categories/{category}', [\App\Http\Controllers\Admin\AdminCategoryController::class, 'update'])
        ->name('categories.update');
    Route::delete('/categories/{category}', [\App\Http\Controllers\Admin\AdminCategoryController::class, 'destroy'])
        ->name('categories.destroy');

    // System Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'index'])
        ->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'update'])
        ->name('settings.update');
});