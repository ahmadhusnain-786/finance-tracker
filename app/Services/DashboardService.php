<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;

class DashboardService
{
    public function resolveDateRange(string $preset, ?string $customFrom = null, ?string $customTo = null): array
    {
        $today = now()->startOfDay();

        return match ($preset) {
            'today' => [
                'from'  => $today->copy(),
                'to'    => $today->copy()->endOfDay(),
                'label' => 'Today',
                'key'   => 'today',
            ],

            'week' => [
                'from'  => $today->copy()->startOfWeek(),
                'to'    => $today->copy()->endOfWeek(),
                'label' => 'This Week',
                'key'   => 'week',
            ],

            'last_month' => [
                'from'  => $today->copy()->subMonthNoOverflow()->startOfMonth(),
                'to'    => $today->copy()->subMonthNoOverflow()->endOfMonth(),
                'label' => 'Last Month',
                'key'   => 'last_month',
            ],

            'year' => [
                'from'  => $today->copy()->startOfYear(),
                'to'    => $today->copy()->endOfYear(),
                'label' => 'This Year',
                'key'   => 'year',
            ],

            'custom' => $this->customRange($customFrom, $customTo, $today),

            default => [
                'from'  => $today->copy()->startOfMonth(),
                'to'    => $today->copy()->endOfMonth(),
                'label' => 'This Month',
                'key'   => 'month',
            ],
        };
    }

    private function customRange(?string $from, ?string $to, Carbon $today): array
    {
        if (! $from || ! $to) {
            return [
                'from'  => $today->copy()->startOfMonth(),
                'to'    => $today->copy()->endOfMonth(),
                'label' => 'This Month',
                'key'   => 'month',
            ];
        }

        try {
            $fromDate = Carbon::parse($from)->startOfDay();
            $toDate   = Carbon::parse($to)->endOfDay();

            if ($fromDate->gt($toDate)) {
                [$fromDate, $toDate] = [$toDate, $fromDate];
            }

            return [
                'from'  => $fromDate,
                'to'    => $toDate,
                'label' => $fromDate->format('d M Y') . ' → ' . $toDate->format('d M Y'),
                'key'   => 'custom',
            ];
        } catch (\Throwable $e) {
            return [
                'from'  => $today->copy()->startOfMonth(),
                'to'    => $today->copy()->endOfMonth(),
                'label' => 'This Month',
                'key'   => 'month',
            ];
        }
    }

    public function getMetrics(int $userId, Carbon $from, Carbon $to): array
    {
        $fromDate = $from->toDateString();
        $toDate   = $to->toDateString();

        return CacheService::remember(
            $userId,
            'dashboard.metrics',
            CacheService::TTL_DASHBOARD,
            fn () => $this->computeMetrics($userId, $fromDate, $toDate),
            $fromDate,
            $toDate,
        );
    }

    private function computeMetrics(int $userId, string $fromDate, string $toDate): array
    {
        $incomeSum = (float) Income::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->sum('amount');

        $expenseSum = (float) Expense::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->sum('amount');

        $incomeCount = (int) Income::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->count();

        $expenseCount = (int) Expense::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->count();

        $balance     = $incomeSum - $expenseSum;
        $savingsRate = $incomeSum > 0
            ? round((($incomeSum - $expenseSum) / $incomeSum) * 100, 2)
            : 0.0;

        $days = max(1, Carbon::parse($fromDate)->diffInDays(Carbon::parse($toDate)) + 1);

        return [
            'income'          => $incomeSum,
            'expenses'        => $expenseSum,
            'balance'         => $balance,
            'savings'         => $balance,
            'savings_rate'    => $savingsRate,
            'income_count'    => $incomeCount,
            'expense_count'   => $expenseCount,
            'avg_income_day'  => round($incomeSum / $days, 2),
            'avg_expense_day' => round($expenseSum / $days, 2),
            'days'            => $days,
        ];
    }

    public function recentTransactions(int $userId, int $limit = 8): array
    {
        $incomes = Income::query()
            ->where('user_id', $userId)
            ->with(['category', 'paymentMethod'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        $expenses = Expense::query()
            ->where('user_id', $userId)
            ->with(['category', 'paymentMethod'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return $incomes->map(fn ($i) => [
            'type'     => 'income',
            'id'       => $i->id,
            'title'    => $i->title,
            'amount'   => (float) $i->amount,
            'date'     => $i->date,
            'category' => $i->category,
            'payment'  => $i->paymentMethod,
            'edit_url' => route('income.edit', $i),
        ])->concat(
            $expenses->map(fn ($e) => [
                'type'     => 'expense',
                'id'       => $e->id,
                'title'    => $e->title,
                'amount'   => (float) $e->amount,
                'date'     => $e->date,
                'category' => $e->category,
                'payment'  => $e->paymentMethod,
                'edit_url' => route('expenses.edit', $e),
            ])
        )
        ->sortByDesc(fn ($r) => $r['date']->timestamp)
        ->take($limit)
        ->values()
        ->all();
    }

    public function getIncomeVsExpenseSeries(int $userId, Carbon $from, Carbon $to): array
    {
        $fromDate = $from->toDateString();
        $toDate   = $to->toDateString();

        return CacheService::remember(
            $userId,
            'dashboard.income_vs_expense',
            CacheService::TTL_DASHBOARD,
            fn () => $this->computeIncomeVsExpenseSeries($userId, $fromDate, $toDate),
            $fromDate,
            $toDate,
        );
    }

    private function computeIncomeVsExpenseSeries(int $userId, string $fromDate, string $toDate): array
    {
        $from = Carbon::parse($fromDate);
        $to   = Carbon::parse($toDate);

        $days    = $from->diffInDays($to) + 1;
        $groupBy = $days <= 31 ? 'day' : 'month';

        $incomes = Income::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->get(['date', 'amount']);

        $expenses = Expense::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->get(['date', 'amount']);

        $buckets = [];

        if ($groupBy === 'day') {
            $cursor = $from->copy()->startOfDay();
            while ($cursor->lte($to)) {
                $buckets[$cursor->format('Y-m-d')] = [
                    'label'   => $cursor->format('d M'),
                    'income'  => 0.0,
                    'expense' => 0.0,
                ];
                $cursor->addDay();
            }
        } else {
            $cursor = $from->copy()->startOfMonth();
            while ($cursor->lte($to)) {
                $buckets[$cursor->format('Y-m')] = [
                    'label'   => $cursor->format('M Y'),
                    'income'  => 0.0,
                    'expense' => 0.0,
                ];
                $cursor->addMonthNoOverflow();
            }
        }

        foreach ($incomes as $row) {
            $key = $groupBy === 'day'
                ? $row->date->format('Y-m-d')
                : $row->date->format('Y-m');

            if (isset($buckets[$key])) {
                $buckets[$key]['income'] += (float) $row->amount;
            }
        }

        foreach ($expenses as $row) {
            $key = $groupBy === 'day'
                ? $row->date->format('Y-m-d')
                : $row->date->format('Y-m');

            if (isset($buckets[$key])) {
                $buckets[$key]['expense'] += (float) $row->amount;
            }
        }

        return [
            'labels'  => array_values(array_map(fn ($b) => $b['label'], $buckets)),
            'income'  => array_values(array_map(fn ($b) => round($b['income'],  2), $buckets)),
            'expense' => array_values(array_map(fn ($b) => round($b['expense'], 2), $buckets)),
            'groupBy' => $groupBy,
        ];
    }

    public function getExpenseByCategory(int $userId, Carbon $from, Carbon $to, int $limit = 8): array
    {
        $fromDate = $from->toDateString();
        $toDate   = $to->toDateString();

        return CacheService::remember(
            $userId,
            'dashboard.expense_by_category',
            CacheService::TTL_DASHBOARD,
            fn () => $this->computeExpenseByCategory($userId, $fromDate, $toDate, $limit),
            $fromDate,
            $toDate,
            (string) $limit,
        );
    }

    private function computeExpenseByCategory(int $userId, string $fromDate, string $toDate, int $limit): array
    {
        $rows = Expense::query()
            ->join('categories', 'categories.id', '=', 'expenses.category_id')
            ->where('expenses.user_id', $userId)
            ->whereBetween('expenses.date', [$fromDate, $toDate])
            ->selectRaw('
                categories.name  as category_name,
                categories.color as category_color,
                SUM(expenses.amount) as total
            ')
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get();

        if ($rows->isEmpty()) {
            return ['labels' => [], 'colors' => [], 'values' => []];
        }

        if ($rows->count() > $limit) {
            $top        = $rows->take($limit - 1);
            $others     = $rows->slice($limit - 1);
            $otherTotal = (float) $others->sum('total');

            $labels = $top->pluck('category_name')->all();
            $colors = $top->pluck('category_color')
                          ->map(fn ($c) => $c ?: '#94a3b8')
                          ->all();
            $values = $top->pluck('total')
                          ->map(fn ($v) => round((float) $v, 2))
                          ->all();

            $labels[] = 'Other';
            $colors[] = '#94a3b8';
            $values[] = round($otherTotal, 2);

            return compact('labels', 'colors', 'values');
        }

        return [
            'labels' => $rows->pluck('category_name')->all(),
            'colors' => $rows->pluck('category_color')
                            ->map(fn ($c) => $c ?: '#94a3b8')
                            ->all(),
            'values' => $rows->pluck('total')
                            ->map(fn ($v) => round((float) $v, 2))
                            ->all(),
        ];
    }

    public function getSavingsTrend(int $userId, Carbon $from, Carbon $to): array
    {
        $fromDate = $from->toDateString();
        $toDate   = $to->toDateString();

        return CacheService::remember(
            $userId,
            'dashboard.savings_trend',
            CacheService::TTL_DASHBOARD,
            fn () => $this->computeSavingsTrend($userId, $fromDate, $toDate),
            $fromDate,
            $toDate,
        );
    }

    private function computeSavingsTrend(int $userId, string $fromDate, string $toDate): array
    {
        $series = $this->computeIncomeVsExpenseSeries($userId, $fromDate, $toDate);

        $cumulative = [];
        $running    = 0.0;

        foreach ($series['labels'] as $i => $_) {
            $running += $series['income'][$i] - $series['expense'][$i];
            $cumulative[] = round($running, 2);
        }

        return [
            'labels' => $series['labels'],
            'values' => $cumulative,
        ];
    }
}