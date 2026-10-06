<?php

namespace App\Services;

use App\Enums\GoalStatus;
use App\Models\Budget;
use App\Models\FinancialGoal;
use App\Models\User;
use App\Notifications\BudgetExceededNotification;
use App\Notifications\BudgetWarningNotification;
use App\Notifications\GoalDeadlineNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function __construct(private BudgetService $budgetService)
    {
    }

    /**
     * Scan every user and generate any needed notifications.
     * Returns [warningCount, exceededCount, goalDeadlineCount].
     */
    public function scanAll(): array
    {
        $warnings  = 0;
        $exceeded  = 0;
        $deadlines = 0;

        User::query()
            ->with(['budgets.category', 'financialGoals'])
            ->chunk(100, function ($users) use (&$warnings, &$exceeded, &$deadlines) {
                foreach ($users as $user) {
                    $w = $this->scanBudgets($user);
                    $d = $this->scanGoals($user);

                    $warnings += $w['warning'];
                    $exceeded += $w['exceeded'];
                    $deadlines += $d;
                }
            });

        return compact('warnings', 'exceeded', 'deadlines');
    }

    /**
     * Scan one user's budgets.
     */
    public function scanBudgets(User $user): array
    {
        $warning = 0;
        $exceeded = 0;

        foreach ($user->budgets()->where('is_active', true)->with('category')->get() as $budget) {
            $s = $this->budgetService->summarize($budget);
            $pct = $s['percentage'];

            if ($pct >= 100) {
                if (! $this->alreadyNotified($user, 'budget_exceeded', 'budget_id', $budget->id)) {
                    $user->notify(new BudgetExceededNotification(
                        $budget, $s['spent'], $s['limit'], $pct
                    ));
                    $exceeded++;
                }
            } elseif ($pct >= 80) {
                if (! $this->alreadyNotified($user, 'budget_warning', 'budget_id', $budget->id)) {
                    $user->notify(new BudgetWarningNotification(
                        $budget, $s['spent'], $s['limit'], $pct
                    ));
                    $warning++;
                }
            }
        }

        return compact('warning', 'exceeded');
    }

    /**
     * Scan one user's goals for upcoming deadlines.
     */
    public function scanGoals(User $user): int
    {
        $count = 0;
        $horizon = now()->addDays(7);

        foreach ($user->financialGoals()
                     ->where('status', GoalStatus::Active->value)
                     ->whereNotNull('target_date')
                     ->whereBetween('target_date', [now()->toDateString(), $horizon->toDateString()])
                     ->get() as $goal) {

            if ($this->alreadyNotified($user, 'goal_deadline', 'goal_id', $goal->id, 3)) {
                continue;
            }

            $daysLeft = (int) now()->startOfDay()->diffInDays($goal->target_date->startOfDay(), false);

            $user->notify(new GoalDeadlineNotification($goal, max(0, $daysLeft)));
            $count++;
        }

        return $count;
    }

    /**
     * Has the user already received a notification of this type about this entity?
     * Checks within the last $days days (default 30 for budgets, lower for goals).
     */
    private function alreadyNotified(
        User $user,
        string $type,
        string $keyName,
        int $keyValue,
        int $days = 30
    ): bool {
        $cutoff = now()->subDays($days);

        return DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', User::class)
            ->where('created_at', '>=', $cutoff)
            ->where('data', 'like', '%"type":"' . $type . '"%')
            ->where('data', 'like', '%"' . $keyName . '":' . $keyValue . '%')
            ->exists();
    }
}