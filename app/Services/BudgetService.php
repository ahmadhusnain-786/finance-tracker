<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Expense;
use Carbon\Carbon;

class BudgetService
{
    /**
     * Get the start/end dates for a budget's current period.
     * Returns [Carbon $start, Carbon $end].
     */
    public function currentPeriodRange(Budget $budget, ?Carbon $now = null): array
    {
        $now = $now ?? now();

        return match ($budget->period) {
            'weekly' => [
                $now->copy()->startOfWeek(),
                $now->copy()->endOfWeek(),
            ],
            'yearly' => [
                $now->copy()->startOfYear(),
                $now->copy()->endOfYear(),
            ],
            default => [ // 'monthly'
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
            ],
        };
    }

    /**
     * Total amount spent in this budget's category during the current period.
     */
    public function spentFor(Budget $budget, ?Carbon $now = null): float
    {
        [$start, $end] = $this->currentPeriodRange($budget, $now);

        return (float) Expense::query()
            ->where('user_id', $budget->user_id)
            ->where('category_id', $budget->category_id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');
    }

    /**
     * Compute all derived fields for a budget:
     * spent, remaining, percentage, status.
     */
    public function summarize(Budget $budget, ?Carbon $now = null): array
    {
        $spent   = $this->spentFor($budget, $now);
        $limit   = (float) $budget->amount;
        $remaining = max(0.0, $limit - $spent);

        $percentage = $limit > 0
            ? round(($spent / $limit) * 100, 2)
            : 0.0;

        [$status, $badgeClass, $progressClass] = $this->status($percentage);

        [$start, $end] = $this->currentPeriodRange($budget, $now);

        return [
            'spent'          => $spent,
            'limit'          => $limit,
            'remaining'      => $remaining,
            'percentage'     => $percentage,
            'status'         => $status,
            'badge_class'    => $badgeClass,
            'progress_class' => $progressClass,
            'period_start'   => $start,
            'period_end'     => $end,
        ];
    }

    /**
     * Returns [statusLabel, badgeClass, progressBarClass] based on %.
     */
    private function status(float $percentage): array
    {
        if ($percentage >= 100) {
            return ['Exceeded',  'bg-danger-subtle text-danger-emphasis',   'bg-danger'];
        }

        if ($percentage >= 80) {
            return ['Warning',   'bg-warning-subtle text-warning-emphasis', 'bg-warning'];
        }

        return ['On Track',  'bg-success-subtle text-success-emphasis', 'bg-success'];
    }

    /**
     * Aggregate the whole collection into totals for the summary strip.
     * Ignores inactive budgets.
     */
    public function totalsFor(iterable $budgets, ?Carbon $now = null): array
    {
        $totalLimit = 0.0;
        $totalSpent = 0.0;
        $exceeded   = 0;
        $warning    = 0;

        foreach ($budgets as $budget) {
            if (! $budget->is_active) {
                continue;
            }

            $s = $this->summarize($budget, $now);

            $totalLimit += $s['limit'];
            $totalSpent += $s['spent'];

            if ($s['status'] === 'Exceeded') $exceeded++;
            if ($s['status'] === 'Warning')  $warning++;
        }

        return [
            'limit'     => round($totalLimit, 2),
            'spent'     => round($totalSpent, 2),
            'remaining' => round(max(0, $totalLimit - $totalSpent), 2),
            'exceeded'  => $exceeded,
            'warning'   => $warning,
        ];
    }
}