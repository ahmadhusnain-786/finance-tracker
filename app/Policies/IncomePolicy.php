<?php

namespace App\Policies;

use App\Models\Income;
use App\Models\User;

class IncomePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        // No admin bypass for financial records — every user only sees their own.
        // Admins do NOT peek at other users' income. Ever.
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Income $income): bool
    {
        return $income->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Income $income): bool
    {
        return $income->user_id === $user->id;
    }

    public function delete(User $user, Income $income): bool
    {
        return $income->user_id === $user->id;
    }
}