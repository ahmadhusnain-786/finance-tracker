<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Resolve a filter into [from, to, label].
     * Accepts: preset (this_month, last_month, this_year, last_year, custom), from, to.
     */
    public function resolveRange(string $preset, ?string $customFrom, ?string $customTo): array
    {
        $today = now()->startOfDay();

        switch ($preset) {
            case 'today':
                return [
                    'from'  => $today->copy(),
                    'to'    => $today->copy()->endOfDay(),
                    'label' => 'Today',
                    'key'   => 'today',
                ];

            case 'last_month':
                return [
                    'from'  => $today->copy()->subMonthNoOverflow()->startOfMonth(),
                    'to'    => $today->copy()->subMonthNoOverflow()->endOfMonth(),
                    'label' => 'Last Month',
                    'key'   => 'last_month',
                ];

            case 'this_year':
                return [
                    'from'  => $today->copy()->startOfYear(),
                    'to'    => $today->copy()->endOfYear(),
                    'label' => 'This Year',
                    'key'   => 'this_year',
                ];

            case 'last_year':
                return [
                    'from'  => $today->copy()->subYear()->startOfYear(),
                    'to'    => $today->copy()->subYear()->endOfYear(),
                    'label' => 'Last Year',
                    'key'   => 'last_year',
                ];

            case 'custom':
                if ($customFrom && $customTo) {
                    try {
                        $f = Carbon::parse($customFrom)->startOfDay();
                        $t = Carbon::parse($customTo)->endOfDay();
                        if ($f->gt($t)) {
                            [$f, $t] = [$t, $f];
                        }
                        return [
                            'from'  => $f,
                            'to'    => $t,
                            'label' => $f->format('d M Y') . ' → ' . $t->format('d M Y'),
                            'key'   => 'custom',
                        ];
                    } catch (\Throwable $e) {
                        // fall through to default
                    }
                }
                // no break — fall through to this_month

            default: // this_month
                return [
                    'from'  => $today->copy()->startOfMonth(),
                    'to'    => $today->copy()->endOfMonth(),
                    'label' => 'This Month',
                    'key'   => 'this_month',
                ];
        }
    }

    /**
     * Income report data — grouped by day or month depending on range.
     */
    public function incomeReport(int $userId, Carbon $from, Carbon $to): array
    {
        $rows = Income::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->with(['category', 'paymentMethod'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        return [
            'rows'      => $rows,
            'total'     => (float) $rows->sum('amount'),
            'count'     => $rows->count(),
            'average'   => $rows->count() > 0 ? round($rows->sum('amount') / $rows->count(), 2) : 0.0,
            'byCategory' => $this->groupByCategory($rows),
            'series'    => $this->bucketSeries($rows, $from, $to),
        ];
    }

    /**
     * Expense report data.
     */
    public function expenseReport(int $userId, Carbon $from, Carbon $to): array
    {
        $rows = Expense::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->with(['category', 'paymentMethod'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        return [
            'rows'      => $rows,
            'total'     => (float) $rows->sum('amount'),
            'count'     => $rows->count(),
            'average'   => $rows->count() > 0 ? round($rows->sum('amount') / $rows->count(), 2) : 0.0,
            'byCategory' => $this->groupByCategory($rows),
            'series'    => $this->bucketSeries($rows, $from, $to),
        ];
    }

    /**
     * Group a collection of income/expense rows by category.
     * Returns array of ['name', 'color', 'icon', 'total', 'count', 'percentage'].
     */
    private function groupByCategory(Collection $rows): array
    {
        $total = (float) $rows->sum('amount');

        $grouped = $rows
            ->groupBy(fn ($r) => $r->category_id)
            ->map(function ($group) use ($total) {
                $cat = $group->first()->category;

                return [
                    'name'       => $cat?->name ?? 'Uncategorized',
                    'color'      => $cat?->color ?? '#94a3b8',
                    'icon'       => $cat?->icon ?? 'bi-tag',
                    'total'      => (float) $group->sum('amount'),
                    'count'      => $group->count(),
                    'percentage' => $total > 0
                        ? round(($group->sum('amount') / $total) * 100, 2)
                        : 0.0,
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->all();

        return $grouped;
    }

    /**
     * Bucket rows by day (≤31 days) or month (>31 days).
     */
    private function bucketSeries(Collection $rows, Carbon $from, Carbon $to): array
    {
        $days = $from->diffInDays($to) + 1;
        $by   = $days <= 31 ? 'day' : 'month';

        $buckets = [];

        if ($by === 'day') {
            $cursor = $from->copy()->startOfDay();
            while ($cursor->lte($to)) {
                $buckets[$cursor->format('Y-m-d')] = [
                    'label' => $cursor->format('d M'),
                    'value' => 0.0,
                ];
                $cursor->addDay();
            }
        } else {
            $cursor = $from->copy()->startOfMonth();
            while ($cursor->lte($to)) {
                $buckets[$cursor->format('Y-m')] = [
                    'label' => $cursor->format('M Y'),
                    'value' => 0.0,
                ];
                $cursor->addMonthNoOverflow();
            }
        }

        foreach ($rows as $row) {
            $key = $by === 'day'
                ? $row->date->format('Y-m-d')
                : $row->date->format('Y-m');

            if (isset($buckets[$key])) {
                $buckets[$key]['value'] += (float) $row->amount;
            }
        }

        return [
            'labels' => array_values(array_map(fn ($b) => $b['label'], $buckets)),
            'values' => array_values(array_map(fn ($b) => round($b['value'], 2), $buckets)),
            'by'     => $by,
        ];
    }
        /**
     * Monthly report — one row per calendar month across the range.
     */
    public function monthlyReport(int $userId, Carbon $from, Carbon $to): array
    {
        $rows = $this->bucketByMonth($userId, $from, $to);

        $totals = [
            'income'   => array_sum(array_column($rows, 'income')),
            'expense'  => array_sum(array_column($rows, 'expense')),
            'balance'  => 0.0,
        ];
        $totals['balance'] = $totals['income'] - $totals['expense'];

        return [
            'rows'   => $rows,
            'totals' => $totals,
        ];
    }

    /**
     * Yearly report — one row per calendar year across the range.
     */
    public function yearlyReport(int $userId, Carbon $from, Carbon $to): array
    {
        $income = Income::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->get(['date', 'amount']);

        $expense = Expense::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->get(['date', 'amount']);

        $buckets = [];
        $cursor = $from->copy()->startOfYear();
        while ($cursor->lte($to)) {
            $buckets[$cursor->format('Y')] = [
                'year'    => $cursor->format('Y'),
                'income'  => 0.0,
                'expense' => 0.0,
                'balance' => 0.0,
            ];
            $cursor->addYear();
        }

        foreach ($income as $r) {
            $k = $r->date->format('Y');
            if (isset($buckets[$k])) $buckets[$k]['income'] += (float) $r->amount;
        }
        foreach ($expense as $r) {
            $k = $r->date->format('Y');
            if (isset($buckets[$k])) $buckets[$k]['expense'] += (float) $r->amount;
        }

        foreach ($buckets as &$b) {
            $b['balance'] = round($b['income'] - $b['expense'], 2);
            $b['income']  = round($b['income'], 2);
            $b['expense'] = round($b['expense'], 2);
        }
        unset($b);

        return ['rows' => array_values($buckets)];
    }

    /**
     * Category report — every category (income + expense) with totals.
     */
    public function categoryReport(int $userId, Carbon $from, Carbon $to): array
    {
        $incomeRows = Income::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->with('category')
            ->get();

        $expenseRows = Expense::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->with('category')
            ->get();

        $incomeByCat  = $this->groupByCategory($incomeRows);
        $expenseByCat = $this->groupByCategory($expenseRows);

        return [
            'income'  => $incomeByCat,
            'expense' => $expenseByCat,
            'totals'  => [
                'income'  => array_sum(array_column($incomeByCat, 'total')),
                'expense' => array_sum(array_column($expenseByCat, 'total')),
            ],
        ];
    }

    /**
     * Budget report — performance of each budget across the period.
     * We look at the current period for each budget (month/week/year).
     */
    public function budgetReport(int $userId, Carbon $from, Carbon $to, BudgetService $budgets): array
    {
        $list = \App\Models\Budget::query()
            ->where('user_id', $userId)
            ->with('category')
            ->orderBy('is_active', 'desc')
            ->orderBy('id')
            ->get();

        $rows = [];
        $totals = ['limit' => 0.0, 'spent' => 0.0, 'over' => 0, 'warning' => 0];

        foreach ($list as $b) {
            // Use the budget's own current period (as budgeting tools do)
            [$start, $end] = $budgets->currentPeriodRange($b);

            $spent = (float) Expense::query()
                ->where('user_id', $userId)
                ->where('category_id', $b->category_id)
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->sum('amount');

            $limit = (float) $b->amount;
            $pct   = $limit > 0 ? round(($spent / $limit) * 100, 2) : 0.0;

            if ($pct >= 100) $totals['over']++;
            elseif ($pct >= 80) $totals['warning']++;

            $totals['limit'] += $limit;
            $totals['spent'] += $spent;

            $rows[] = [
                'budget'   => $b,
                'period'   => [$start, $end],
                'limit'    => $limit,
                'spent'    => $spent,
                'remaining'=> max(0.0, $limit - $spent),
                'pct'      => $pct,
                'status'   => $pct >= 100 ? 'Exceeded' : ($pct >= 80 ? 'Warning' : 'On Track'),
                'bar'      => $pct >= 100 ? 'bg-danger' : ($pct >= 80 ? 'bg-warning' : 'bg-success'),
            ];
        }

        return ['rows' => $rows, 'totals' => $totals];
    }

    /**
     * Savings report — cumulative savings across month buckets in the range.
     */
    public function savingsReport(int $userId, Carbon $from, Carbon $to): array
    {
        $rows = $this->bucketByMonth($userId, $from, $to);

        $cumulative = 0.0;
        $savings = [];
        foreach ($rows as $r) {
            $cumulative += $r['income'] - $r['expense'];
            $savings[] = [
                'label'    => $r['label'],
                'income'   => $r['income'],
                'expense'  => $r['expense'],
                'monthly'  => round($r['income'] - $r['expense'], 2),
                'savings'  => round($cumulative, 2),
                'rate'     => $r['income'] > 0
                    ? round((($r['income'] - $r['expense']) / $r['income']) * 100, 2)
                    : 0.0,
            ];
        }

        $totalIncome  = array_sum(array_column($rows, 'income'));
        $totalExpense = array_sum(array_column($rows, 'expense'));

        return [
            'rows'     => $savings,
            'totals'   => [
                'income'  => $totalIncome,
                'expense' => $totalExpense,
                'savings' => $totalIncome - $totalExpense,
                'rate'    => $totalIncome > 0
                    ? round((($totalIncome - $totalExpense) / $totalIncome) * 100, 2)
                    : 0.0,
            ],
        ];
    }

    /**
     * Executive summary — compact metrics + top categories.
     */
    public function financialSummary(int $userId, Carbon $from, Carbon $to): array
    {
        $incomeRows = Income::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->with('category')
            ->get();

        $expenseRows = Expense::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->with('category')
            ->get();

        $totalIncome  = (float) $incomeRows->sum('amount');
        $totalExpense = (float) $expenseRows->sum('amount');
        $balance      = $totalIncome - $totalExpense;
        $rate         = $totalIncome > 0 ? round(($balance / $totalIncome) * 100, 2) : 0.0;

        $topIncome  = array_slice($this->groupByCategory($incomeRows),  0, 5);
        $topExpense = array_slice($this->groupByCategory($expenseRows), 0, 5);

        return [
            'income'         => $totalIncome,
            'expense'        => $totalExpense,
            'balance'        => $balance,
            'savings_rate'   => $rate,
            'income_count'   => $incomeRows->count(),
            'expense_count'  => $expenseRows->count(),
            'top_income'     => $topIncome,
            'top_expense'    => $topExpense,
        ];
    }

    /**
     * Shared bucket-by-month helper.
     */
    private function bucketByMonth(int $userId, Carbon $from, Carbon $to): array
    {
        $income = Income::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->get(['date', 'amount']);

        $expense = Expense::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->get(['date', 'amount']);

        $buckets = [];
        $cursor = $from->copy()->startOfMonth();
        while ($cursor->lte($to)) {
            $key = $cursor->format('Y-m');
            $buckets[$key] = [
                'label'   => $cursor->format('M Y'),
                'income'  => 0.0,
                'expense' => 0.0,
            ];
            $cursor->addMonthNoOverflow();
        }

        foreach ($income as $r) {
            $k = $r->date->format('Y-m');
            if (isset($buckets[$k])) $buckets[$k]['income'] += (float) $r->amount;
        }
        foreach ($expense as $r) {
            $k = $r->date->format('Y-m');
            if (isset($buckets[$k])) $buckets[$k]['expense'] += (float) $r->amount;
        }

        foreach ($buckets as &$b) {
            $b['income']  = round($b['income'], 2);
            $b['expense'] = round($b['expense'], 2);
        }
        unset($b);

        return array_values($buckets);
    }
}