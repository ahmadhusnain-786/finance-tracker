<?php

namespace App\Services;

use App\Enums\GoalStatus;
use App\Models\FinancialGoal;
use App\Models\GoalContribution;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoalService
{
    /**
     * Add a contribution and recalculate the goal's current_amount.
     */
    public function addContribution(FinancialGoal $goal, array $data): GoalContribution
    {
        return DB::transaction(function () use ($goal, $data) {
            $contribution = $goal->contributions()->create([
                'user_id'           => $goal->user_id,
                'amount'            => $data['amount'],
                'contribution_date' => $data['contribution_date'],
                'note'              => $data['note'] ?? null,
            ]);

            $this->recalculate($goal);

            return $contribution;
        });
    }

    /**
     * Delete a contribution and recalculate.
     */
    public function deleteContribution(GoalContribution $contribution): void
    {
        DB::transaction(function () use ($contribution) {
            $goal = $contribution->financialGoal;

            if (! $goal) {
                return;
            }

            $contribution->delete();

            $this->recalculate($goal);
        });
    }

    /**
     * Sum contributions and update current_amount + auto-complete logic.
     * This is the ONLY place that should ever write to current_amount.
     */
    public function recalculate(FinancialGoal $goal): void
    {
        $total = (float) $goal->contributions()->sum('amount');

        $goal->current_amount = $total;

        // Auto-flip status if the goal is reached (only if still active)
        if ($goal->status === GoalStatus::Active
            && (float) $goal->target_amount > 0
            && $total >= (float) $goal->target_amount) {
            $goal->status = GoalStatus::Completed;
        }

        // If a completed goal drops below target (contribution removed),
        // reactivate it.
        if ($goal->status === GoalStatus::Completed
            && (float) $goal->target_amount > 0
            && $total < (float) $goal->target_amount) {
            $goal->status = GoalStatus::Active;
        }

        $goal->save();

        Log::debug('Goal recalculated', [
            'goal_id'        => $goal->id,
            'current_amount' => $total,
            'status'         => $goal->status->value,
        ]);
    }

    /**
     * Aggregate stats for the goals index header.
     */
    public function totalsFor(iterable $goals): array
    {
        $target  = 0.0;
        $saved   = 0.0;
        $active  = 0;
        $completed = 0;

        foreach ($goals as $g) {
            if ($g->status === GoalStatus::Cancelled) {
                continue;
            }

            $target += (float) $g->target_amount;
            $saved  += (float) $g->current_amount;

            if ($g->status === GoalStatus::Completed) $completed++;
            if ($g->status === GoalStatus::Active)    $active++;
        }

        return [
            'target'    => round($target, 2),
            'saved'     => round($saved, 2),
            'remaining' => round(max(0, $target - $saved), 2),
            'active'    => $active,
            'completed' => $completed,
        ];
    }
}