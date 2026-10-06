<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * System-wide admin bypass.
     */
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

    public function view(User $user, Category $category): bool
    {
        // System categories (user_id NULL) are viewable by all
        if ($category->user_id === null) {
            return true;
        }

        return $category->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Category $category): bool
    {
        // Can't edit a system category
        if ($category->is_system) {
            return false;
        }

        return $category->user_id === $user->id;
    }

    public function delete(User $user, Category $category): bool
    {
        // Can't delete system categories
        if ($category->is_system) {
            return false;
        }

        // Can't delete if it's in use
        if ($category->income()->exists() || $category->expenses()->exists()) {
            return false;
        }

        return $category->user_id === $user->id;
    }
}