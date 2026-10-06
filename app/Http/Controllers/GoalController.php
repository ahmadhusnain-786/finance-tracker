<?php

namespace App\Http\Controllers;

use App\Enums\GoalStatus;
use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\FinancialGoal;
use App\Services\GoalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GoalController extends Controller
{
    public function __construct(private GoalService $goals)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', FinancialGoal::class);

        $userId = $request->user()->id;

        $query = FinancialGoal::query()->where('user_id', $userId);

        // Status filter
        $status = $request->input('status', 'all');
        if (in_array($status, ['active', 'completed', 'cancelled'], true)) {
            $query->where('status', $status);
        }

        // Search
        if ($q = trim((string) $request->input('q'))) {
            $query->where('name', 'like', "%{$q}%");
        }

        $goals = $query
            ->orderByRaw("CASE status WHEN 'active' THEN 1 WHEN 'completed' THEN 2 ELSE 3 END")
            ->orderByRaw('target_date IS NULL ASC')
            ->orderBy('target_date')
            ->get();

        $totals = $this->goals->totalsFor($goals);

        return view('goals.index', [
            'goals'    => $goals,
            'totals'   => $totals,
            'filters'  => ['status' => $status, 'q' => $request->input('q')],
            'currency' => $request->user()->profile->currency ?? 'PKR',
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', FinancialGoal::class);

        return view('goals.create', [
            'goal' => new FinancialGoal([
                'status'      => GoalStatus::Active->value,
                'target_date' => now()->addMonths(6)->toDateString(),
            ]),
        ]);
    }

    public function store(StoreGoalRequest $request): RedirectResponse
    {
        $this->authorize('create', FinancialGoal::class);

        $data = $request->validated();
        $data['user_id']        = $request->user()->id;
        $data['current_amount'] = 0;
        $data['status']         = $data['status'] ?? GoalStatus::Active->value;

        FinancialGoal::create($data);

        return redirect()
            ->route('goals.index')
            ->with('success', 'Goal created successfully.');
    }

    public function show(Request $request, FinancialGoal $goal): View
    {
        $this->authorize('view', $goal);

        $goal->load(['contributions' => function ($q) {
            $q->orderByDesc('contribution_date')->orderByDesc('id');
        }]);

        return view('goals.show', [
            'goal'     => $goal,
            'currency' => $request->user()->profile->currency ?? 'PKR',
        ]);
    }

    public function edit(Request $request, FinancialGoal $goal): View
    {
        $this->authorize('update', $goal);

        return view('goals.edit', [
            'goal'     => $goal,
            'currency' => $request->user()->profile->currency ?? 'PKR',
        ]);
    }

    public function update(UpdateGoalRequest $request, FinancialGoal $goal): RedirectResponse
    {
        $this->authorize('update', $goal);

        $data = $request->validated();

        // Don't let the user manually set current_amount. It's ignored anyway.
        unset($data['current_amount']);

        $goal->update($data);

        // Recalculate in case the target_amount changed (auto-complete status may flip)
        $this->goals->recalculate($goal);

        return redirect()
            ->route('goals.show', $goal)
            ->with('success', 'Goal updated successfully.');
    }

    public function destroy(FinancialGoal $goal): RedirectResponse
    {
        $this->authorize('delete', $goal);

        $goal->delete();

        return redirect()
            ->route('goals.index')
            ->with('success', 'Goal deleted successfully.');
    }
}