<?php

namespace App\Providers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Expense;
use App\Models\FinancialGoal;
use App\Models\Income;
use App\Models\PaymentMethod;
use App\Models\RecurringTransaction;
use App\Observers\CategoryObserver;
use App\Observers\ExpenseObserver;
use App\Observers\IncomeObserver;
use App\Observers\PaymentMethodObserver;
use App\Policies\BudgetPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\FinancialGoalPolicy;
use App\Policies\IncomePolicy;
use App\Policies\PaymentMethodPolicy;
use App\Policies\RecurringTransactionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Policies
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(PaymentMethod::class, PaymentMethodPolicy::class);
        Gate::policy(Income::class, IncomePolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);
        Gate::policy(Budget::class, BudgetPolicy::class);
        Gate::policy(FinancialGoal::class, FinancialGoalPolicy::class);
        Gate::policy(RecurringTransaction::class, RecurringTransactionPolicy::class);

        // Cache invalidation observers
        Income::observe(IncomeObserver::class);
        Expense::observe(ExpenseObserver::class);
        Category::observe(CategoryObserver::class);
        PaymentMethod::observe(PaymentMethodObserver::class);
    }
}