<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;

class BudgetPolicy
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

    public function view(User $user, Budget $budget): bool
    {
        return $budget->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Budget $budget): bool
    {
        return $budget->user_id === $user->id;
    }

    public function delete(User $user, Budget $budget): bool
    {
        return $budget->user_id === $user->id;
    }
}