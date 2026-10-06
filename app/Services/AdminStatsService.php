<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\FinancialGoal;
use App\Models\Income;
use App\Models\User;

class AdminStatsService
{
    /**
     * System-wide statistics. No user's private financial details leak.
     */
    public function systemStats(): array
    {
        return [
            'total_users'         => User::count(),
            'active_users'        => User::where('is_active', true)->count(),
            'inactive_users'      => User::where('is_active', false)->count(),
            'admin_users'         => User::where('role', UserRole::Admin->value)->count(),
            'regular_users'       => User::where('role', UserRole::User->value)->count(),

            // Aggregate counts (not amounts — those are private)
            'total_income_records'   => Income::count(),
            'total_expense_records'  => Expense::count(),
            'total_budgets'          => Budget::count(),
            'total_goals'            => FinancialGoal::count(),

            // Recently registered / logged in
            'users_today'         => User::whereDate('created_at', today())->count(),
            'users_this_week'     => User::where('created_at', '>=', now()->startOfWeek())->count(),
            'logged_in_today'     => User::whereDate('last_login_at', today())->count(),
        ];
    }

    /**
     * Latest 8 registered users.
     */
    public function recentUsers(int $limit = 8)
    {
        return User::query()
            ->with('profile')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Latest 8 login events.
     */
    public function recentLogins(int $limit = 8)
    {
        return User::query()
            ->whereNotNull('last_login_at')
            ->orderByDesc('last_login_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Aggregate-only activity per user, for admin visibility without exposing
     * the actual amounts. Only shows the count of records per user.
     */
    public function topActiveUsersByRecordCount(int $limit = 5)
    {
        return User::query()
            ->withCount(['income', 'expenses', 'budgets', 'financialGoals'])
            ->orderByRaw('(income_count + expenses_count + budgets_count + financial_goals_count) DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * Full paginated user list with filters (used by AdminUserController@index).
     * Actually the controller does this directly — this method is for reuse.
     */
    public function userActivityCounts(User $user): array
    {
        return [
            'income'    => $user->income()->count(),
            'expense'   => $user->expenses()->count(),
            'budgets'   => $user->budgets()->count(),
            'goals'     => $user->financialGoals()->count(),
            'recurring' => $user->recurringTransactions()->count(),
        ];
    }
}