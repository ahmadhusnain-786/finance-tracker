<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\UpdateIncomeRequest;
use App\Models\Category;
use App\Models\Income;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncomeController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Income::class);

        $userId = $request->user()->id;

        $query = Income::query()
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

        $income = $query->paginate(15)->withQueryString();

        return view('income.index', [
            'income'         => $income,
            'total'          => $total,
            'count'          => $count,
            'categories'     => $this->incomeCategories($userId),
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
        $this->authorize('create', Income::class);

        $userId = $request->user()->id;

        return view('income.create', [
            'income' => new Income([
                'date'         => now()->toDateString(),
                'is_recurring' => false,
            ]),
            'categories'     => $this->incomeCategories($userId),
            'paymentMethods' => $this->paymentMethods($userId),
        ]);
    }

    public function store(StoreIncomeRequest $request): RedirectResponse
    {
        $this->authorize('create', Income::class);

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['is_recurring'] = $request->boolean('is_recurring');

        if (! $data['is_recurring']) {
            $data['recurring_frequency'] = null;
        }

        Income::create($data);

        return redirect()
            ->route('income.index')
            ->with('success', 'Income added successfully.');
    }

    public function edit(Request $request, Income $income): View
    {
        $this->authorize('update', $income);

        $userId = $request->user()->id;

        return view('income.edit', [
            'income'         => $income,
            'categories'     => $this->incomeCategories($userId),
            'paymentMethods' => $this->paymentMethods($userId),
        ]);
    }

    public function update(UpdateIncomeRequest $request, Income $income): RedirectResponse
    {
        $this->authorize('update', $income);

        $data = $request->validated();
        $data['is_recurring'] = $request->boolean('is_recurring');

        if (! $data['is_recurring']) {
            $data['recurring_frequency'] = null;
        }

        $income->update($data);

        return redirect()
            ->route('income.index')
            ->with('success', 'Income updated successfully.');
    }

    public function destroy(Income $income): RedirectResponse
    {
        $this->authorize('delete', $income);

        $income->delete();

        return redirect()
            ->route('income.index')
            ->with('success', 'Income deleted successfully.');
    }

    private function incomeCategories(int $userId)
    {
        return Category::query()
            ->availableTo($userId)
            ->ofType(CategoryType::Income)
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