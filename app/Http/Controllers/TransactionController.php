<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class TransactionController extends Controller
{
    private const PER_PAGE = 20;

    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        // ---- Read filters ----
        $search     = trim((string) $request->input('q'));
        $type       = $request->input('type');           // 'income' | 'expense' | null
        $categoryId = $request->input('category_id');
        $paymentId  = $request->input('payment_method_id');
        $from       = $request->input('from_date');
        $to         = $request->input('to_date');
        $sort       = $request->input('sort', 'date_desc');

        // ---- Build base queries ----
        $incomeQuery  = Income::query()
            ->where('user_id', $userId)
            ->with(['category', 'paymentMethod']);

        $expenseQuery = Expense::query()
            ->where('user_id', $userId)
            ->with(['category', 'paymentMethod']);

        // ---- Apply shared filters to both ----
        $applyFilters = function ($q) use ($search, $categoryId, $paymentId, $from, $to) {
            if ($search !== '') {
                $q->where('title', 'like', "%{$search}%");
            }
            if ($categoryId) {
                $q->where('category_id', $categoryId);
            }
            if ($paymentId) {
                $q->where('payment_method_id', $paymentId);
            }
            if ($from) {
                $q->whereDate('date', '>=', $from);
            }
            if ($to) {
                $q->whereDate('date', '<=', $to);
            }
            return $q;
        };

        $applyFilters($incomeQuery);
        $applyFilters($expenseQuery);

        // ---- Fetch (respecting type filter) ----
        $incomes  = ($type === 'expense') ? collect() : $incomeQuery->get();
        $expenses = ($type === 'income')  ? collect() : $expenseQuery->get();

        // ---- Merge into a unified collection ----
        $rows = $this->merge($incomes, $expenses);

        // ---- Sort ----
        $rows = match ($sort) {
            'date_asc'    => $rows->sortBy(fn ($r) => $r->date->timestamp),
            'amount_desc' => $rows->sortByDesc(fn ($r) => (float) $r->amount),
            'amount_asc'  => $rows->sortBy(fn ($r) => (float) $r->amount),
            'title_asc'   => $rows->sortBy(fn ($r) => $r->title),
            default       => $rows->sortByDesc(fn ($r) => $r->date->timestamp)
                                   ->values(),  // stable: newest first
        };

        $rows = $rows->values();

        // ---- Totals (before pagination) ----
        $totalIncome  = (float) $incomes->sum('amount');
        $totalExpense = (float) $expenses->sum('amount');
        $netBalance   = $totalIncome - $totalExpense;

        // ---- Paginate manually ----
        $perPage = self::PER_PAGE;
        $page    = max(1, (int) $request->input('page', 1));
        $total   = $rows->count();
        $slice   = $rows->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]
        );

        // ---- Dropdown data ----
        $categories     = Category::query()
            ->availableTo($userId)
            ->orderBy('name')
            ->get();

        $paymentMethods = PaymentMethod::query()
            ->availableTo($userId)
            ->orderBy('name')
            ->get();

        return view('transactions.index', [
            'transactions'   => $paginator,
            'totalIncome'    => $totalIncome,
            'totalExpense'   => $totalExpense,
            'netBalance'     => $netBalance,
            'categories'     => $categories,
            'paymentMethods' => $paymentMethods,
            'filters' => [
                'q'                 => $request->input('q'),
                'type'              => $type,
                'category_id'       => $categoryId,
                'payment_method_id' => $paymentId,
                'from_date'         => $from,
                'to_date'           => $to,
                'sort'              => $sort,
            ],
        ]);
    }

    /**
     * Merge income + expense models into a single collection of lightweight row objects.
     * Each row exposes: id, type, title, amount, date, category, paymentMethod, editable, model.
     */
    private function merge(Collection $incomes, Collection $expenses): Collection
    {
        $mapIncome = $incomes->map(function (Income $i) {
            return (object) [
                'id'            => $i->id,
                'type'          => 'income',
                'title'         => $i->title,
                'amount'        => $i->amount,
                'date'          => $i->date,
                'description'   => $i->description,
                'is_recurring'  => $i->is_recurring,
                'frequency'     => $i->recurring_frequency,
                'category'      => $i->category,
                'paymentMethod' => $i->paymentMethod,
                'edit_route'    => route('income.edit', $i),
                'delete_route'  => route('income.destroy', $i),
            ];
        });

        $mapExpense = $expenses->map(function (Expense $e) {
            return (object) [
                'id'            => $e->id,
                'type'          => 'expense',
                'title'         => $e->title,
                'amount'        => $e->amount,
                'date'          => $e->date,
                'description'   => $e->description,
                'is_recurring'  => $e->is_recurring,
                'frequency'     => $e->recurring_frequency,
                'category'      => $e->category,
                'paymentMethod' => $e->paymentMethod,
                'edit_route'    => route('expenses.edit', $e),
                'delete_route'  => route('expenses.destroy', $e),
            ];
        });

        return $mapIncome->concat($mapExpense);
    }
}