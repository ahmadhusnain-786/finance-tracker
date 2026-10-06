<?php

namespace App\Policies;

use App\Models\FinancialGoal;
use App\Models\User;

class FinancialGoalPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        // Financial data is private — no admin bypass.
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, FinancialGoal $goal): bool
    {
        return $goal->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, FinancialGoal $goal): bool
    {
        return $goal->user_id === $user->id;
    }

    public function delete(User $user, FinancialGoal $goal): bool
    {
        return $goal->user_id === $user->id;
    }

    /**
     * Contribute to a goal.
     */
    public function contribute(User $user, FinancialGoal $goal): bool
    {
        return $goal->user_id === $user->id;
    }
}