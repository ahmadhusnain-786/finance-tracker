<?php

namespace Tests\Feature\Dashboard;

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_metrics_calculation_is_correct(): void
    {
        $user = User::factory()->create();

        Income::factory()->create([
            'user_id' => $user->id,
            'amount'  => 300000,
            'date'    => now()->toDateString(),
        ]);

        Expense::factory()->create([
            'user_id' => $user->id,
            'amount'  => 50000,
            'date'    => now()->toDateString(),
        ]);

        Expense::factory()->create([
            'user_id' => $user->id,
            'amount'  => 55000,
            'date'    => now()->toDateString(),
        ]);

        $service = app(DashboardService::class);
        $range   = $service->resolveDateRange('month');
        $metrics = $service->getMetrics($user->id, $range['from'], $range['to']);

        $this->assertSame(300000.0, $metrics['income']);
        $this->assertSame(105000.0, $metrics['expenses']);
        $this->assertSame(195000.0, $metrics['balance']);
        $this->assertSame(195000.0, $metrics['savings']);
        $this->assertSame(65.0, $metrics['savings_rate']);
    }

    public function test_savings_rate_is_zero_when_no_income(): void
    {
        $user = User::factory()->create();

        Expense::factory()->create([
            'user_id' => $user->id,
            'amount'  => 5000,
            'date'    => now()->toDateString(),
        ]);

        $service = app(DashboardService::class);
        $range   = $service->resolveDateRange('month');
        $metrics = $service->getMetrics($user->id, $range['from'], $range['to']);

        $this->assertSame(0.0, $metrics['savings_rate']);
    }

    public function test_metrics_respect_date_range(): void
    {
        $user = User::factory()->create();

        // This month
        Income::factory()->create([
            'user_id' => $user->id,
            'amount'  => 1000,
            'date'    => now()->toDateString(),
        ]);

        // Last month
        Income::factory()->create([
            'user_id' => $user->id,
            'amount'  => 9999,
            'date'    => now()->subMonth()->toDateString(),
        ]);

        $service = app(DashboardService::class);
        $range   = $service->resolveDateRange('month');
        $metrics = $service->getMetrics($user->id, $range['from'], $range['to']);

        $this->assertSame(1000.0, $metrics['income']);
    }
}