<?php

namespace App\Observers;

use App\Models\Expense;
use App\Services\CacheService;

class ExpenseObserver
{
    public function created(Expense $expense): void
    {
        CacheService::bust($expense->user_id);
    }

    public function updated(Expense $expense): void
    {
        CacheService::bust($expense->user_id);

        if ($expense->wasChanged('user_id')) {
            CacheService::bust($expense->getOriginal('user_id'));
        }
    }

    public function deleted(Expense $expense): void
    {
        CacheService::bust($expense->user_id);
    }
}