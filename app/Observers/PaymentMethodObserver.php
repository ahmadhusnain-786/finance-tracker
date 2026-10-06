<?php

namespace App\Observers;

use App\Models\PaymentMethod;
use App\Services\CacheService;

class PaymentMethodObserver
{
    public function saved(PaymentMethod $method): void
    {
        if ($method->user_id) {
            CacheService::bust($method->user_id);
        }
    }

    public function deleted(PaymentMethod $method): void
    {
        if ($method->user_id) {
            CacheService::bust($method->user_id);
        }
    }
}