<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Budget;
use App\Models\Category;
use App\Services\BudgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function __construct(private BudgetService $budgets)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Budget::class);

        $userId = $request->user()->id;

        $query = Budget::query()
            ->where('user_id', $userId)
            ->with('category');

        // Filter by status: active / inactive / all
        $status = $request->input('status', 'active');
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        // Search by category name
        if ($q = trim((string) $request->input('q'))) {
            $query->whereHas('category', fn ($cq) => $cq->where('name', 'like', "%{$q}%"));
        }

        $budgets = $query->orderByDesc('is_active')->orderBy('id')->get();

        // Attach summary to each budget as a dynamic property
        $summaries = [];
        foreach ($budgets as $b) {
            $summaries[$b->id] = $this->budgets->summarize($b);
        }

        $totals = $this->budgets->totalsFor($budgets);

        return view('budgets.index', [
            'budgets'    => $budgets,
            'summaries'  => $summaries,
            'totals'     => $totals,
            'filters'    => [
                'status' => $status,
                'q'      => $request->input('q'),
            ],
            'currency'   => $request->user()->profile->currency ?? 'PKR',
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Budget::class);

        return view('budgets.create', [
            'budget'     => new Budget([
                'period'    => 'monthly',
                'is_active' => true,
            ]),
            'categories' => $this->expenseCategories($request->user()->id),
            'usedCategoryIds' => $this->usedCategoryIds($request->user()->id),
        ]);
    }

    public function store(StoreBudgetRequest $request): RedirectResponse
    {
        $this->authorize('create', Budget::class);

        $data = $request->validated();
        $data['user_id']   = $request->user()->id;
        $data['is_active'] = $request->boolean('is_active', true);

        Budget::create($data);

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Budget created successfully.');
    }

    public function edit(Request $request, Budget $budget): View
    {
        $this->authorize('update', $budget);

        return view('budgets.edit', [
            'budget'     => $budget,
            'categories' => $this->expenseCategories($request->user()->id),
            'usedCategoryIds' => $this->usedCategoryIds($request->user()->id, $budget->id),
            'summary'    => $this->budgets->summarize($budget),
        ]);
    }

    public function update(UpdateBudgetRequest $request, Budget $budget): RedirectResponse
    {
        $this->authorize('update', $budget);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $budget->update($data);

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Budget updated successfully.');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        $this->authorize('delete', $budget);

        $budget->delete();

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Budget deleted successfully.');
    }

    private function expenseCategories(int $userId)
    {
        return Category::query()
            ->availableTo($userId)
            ->ofType(CategoryType::Expense)
            ->orderBy('name')
            ->get();
    }

    /**
     * Return array of category IDs already used by a budget for this user,
     * so the create/edit form can hide/disable them.
     */
    private function usedCategoryIds(int $userId, ?int $exceptBudgetId = null): array
    {
        return Budget::query()
            ->where('user_id', $userId)
            ->where('period', 'monthly')
            ->when($exceptBudgetId, fn ($q) => $q->where('id', '!=', $exceptBudgetId))
            ->pluck('category_id')
            ->all();
    }
}