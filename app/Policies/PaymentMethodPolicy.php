<?php

namespace App\Policies;

use App\Models\PaymentMethod;
use App\Models\User;

class PaymentMethodPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PaymentMethod $method): bool
    {
        if ($method->user_id === null) {
            return true;
        }

        return $method->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PaymentMethod $method): bool
    {
        if ($method->is_system) {
            return false;
        }

        return $method->user_id === $user->id;
    }

    public function delete(User $user, PaymentMethod $method): bool
    {
        if ($method->is_system) {
            return false;
        }

        // Block delete if any income/expense or recurring entry is using it
        if ($method->income()->exists()
            || $method->expenses()->exists()
            || $method->recurringTransactions()->exists()) {
            return false;
        }

        return $method->user_id === $user->id;
    }
}