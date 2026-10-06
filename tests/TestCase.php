<?php

namespace Tests;

use Database\Seeders\DefaultCategorySeeder;
use Database\Seeders\DefaultPaymentMethodSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Seed default categories + payment methods for tests that need them.
     * Call from within a test with: $this->seedDefaults();
     */
    protected function seedDefaults(): void
    {
        $this->seed(DefaultCategorySeeder::class);
        $this->seed(DefaultPaymentMethodSeeder::class);
    }
}