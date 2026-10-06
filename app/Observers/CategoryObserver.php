<?php

namespace App\Observers;

use App\Models\Category;
use App\Services\CacheService;

class CategoryObserver
{
    public function saved(Category $category): void
    {
        if ($category->user_id) {
            CacheService::bust($category->user_id);
        }
    }

    public function deleted(Category $category): void
    {
        if ($category->user_id) {
            CacheService::bust($category->user_id);
        }
    }
}