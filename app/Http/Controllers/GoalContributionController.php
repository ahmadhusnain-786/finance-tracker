<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContributionRequest;
use App\Models\FinancialGoal;
use App\Models\GoalContribution;
use App\Services\GoalService;
use Illuminate\Http\RedirectResponse;

class GoalContributionController extends Controller
{
    public function __construct(private GoalService $goals)
    {
    }

    public function store(StoreContributionRequest $request, FinancialGoal $goal): RedirectResponse
    {
        $this->authorize('contribute', $goal);

        $this->goals->addContribution($goal, $request->validated());

        return redirect()
            ->route('goals.show', $goal)
            ->with('success', 'Contribution added successfully.');
    }

    public function destroy(FinancialGoal $goal, GoalContribution $contribution): RedirectResponse
    {
        $this->authorize('contribute', $goal);

        // Safety: the contribution must belong to this goal
        if ($contribution->financial_goal_id !== $goal->id) {
            abort(404);
        }

        $this->goals->deleteContribution($contribution);

        return redirect()
            ->route('goals.show', $goal)
            ->with('success', 'Contribution deleted successfully.');
    }
}