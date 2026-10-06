<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Category;
use App\Models\Expense;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Expense::class);

        $userId = $request->user()->id;

        $query = Expense::query()
            ->where('user_id', $userId)
            ->with(['category', 'paymentMethod']);

        if ($search = trim((string) $request->input('q'))) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($paymentId = $request->input('payment_method_id')) {
            $query->where('payment_method_id', $paymentId);
        }

        if ($from = $request->input('from_date')) {
            $query->whereDate('date', '>=', $from);
        }
        if ($to = $request->input('to_date')) {
            $query->whereDate('date', '<=', $to);
        }

        $sort = $request->input('sort', 'date_desc');
        match ($sort) {
            'date_asc'    => $query->orderBy('date')->orderBy('id'),
            'amount_desc' => $query->orderByDesc('amount'),
            'amount_asc'  => $query->orderBy('amount'),
            'title_asc'   => $query->orderBy('title'),
            default       => $query->orderByDesc('date')->orderByDesc('id'),
        };

        $totalQuery = clone $query;
        $total      = (float) $totalQuery->sum('amount');
        $count      = (int)   $totalQuery->count();

        $expenses = $query->paginate(15)->withQueryString();

        return view('expenses.index', [
            'expenses'       => $expenses,
            'total'          => $total,
            'count'          => $count,
            'categories'     => $this->expenseCategories($userId),
            'paymentMethods' => $this->paymentMethods($userId),
            'filters' => [
                'q'                 => $request->input('q'),
                'category_id'       => $request->input('category_id'),
                'payment_method_id' => $request->input('payment_method_id'),
                'from_date'         => $request->input('from_date'),
                'to_date'           => $request->input('to_date'),
                'sort'              => $sort,
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Expense::class);

        $userId = $request->user()->id;

        return view('expenses.create', [
            'expense' => new Expense([
                'date'         => now()->toDateString(),
                'is_recurring' => false,
            ]),
            'categories'     => $this->expenseCategories($userId),
            'paymentMethods' => $this->paymentMethods($userId),
        ]);
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $this->authorize('create', Expense::class);

        $data = $request->validated();
        $data['user_id']      = $request->user()->id;
        $data['is_recurring'] = $request->boolean('is_recurring');

        if (! $data['is_recurring']) {
            $data['recurring_frequency'] = null;
        }

        Expense::create($data);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense added successfully.');
    }

    public function edit(Request $request, Expense $expense): View
    {
        $this->authorize('update', $expense);

        $userId = $request->user()->id;

        return view('expenses.edit', [
            'expense'        => $expense,
            'categories'     => $this->expenseCategories($userId),
            'paymentMethods' => $this->paymentMethods($userId),
        ]);
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $this->authorize('update', $expense);

        $data = $request->validated();
        $data['is_recurring'] = $request->boolean('is_recurring');

        if (! $data['is_recurring']) {
            $data['recurring_frequency'] = null;
        }

        $expense->update($data);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $this->authorize('delete', $expense);

        $expense->delete();

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    private function expenseCategories(int $userId)
    {
        return Category::query()
            ->availableTo($userId)
            ->ofType(CategoryType::Expense)
            ->orderBy('name')
            ->get();
    }

    private function paymentMethods(int $userId)
    {
        return PaymentMethod::query()
            ->availableTo($userId)
            ->orderBy('name')
            ->get();
    }
}