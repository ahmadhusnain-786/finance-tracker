<?php

namespace App\Observers;

use App\Models\Income;
use App\Services\CacheService;

class IncomeObserver
{
    public function created(Income $income): void
    {
        CacheService::bust($income->user_id);
    }

    public function updated(Income $income): void
    {
        CacheService::bust($income->user_id);

        if ($income->wasChanged('user_id')) {
            CacheService::bust($income->getOriginal('user_id'));
        }
    }

    public function deleted(Income $income): void
    {
        CacheService::bust($income->user_id);
    }
}