<?php

namespace Tests\Feature\Budget;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use App\Services\BudgetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDefaults();
    }

    public function test_budget_spent_is_calculated_server_side(): void
    {
        $user     = User::factory()->create();
        $category = Category::where('type', 'expense')->first();

        $budget = Budget::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'amount'      => 40000,
            'period'      => 'monthly',
        ]);

        // Spend 25000 this month
        Expense::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'amount'      => 25000,
            'date'        => now()->startOfMonth()->addDays(3),
        ]);

        $service = app(BudgetService::class);
        $summary = $service->summarize($budget);

        $this->assertSame(25000.0, $summary['spent']);
        $this->assertSame(40000.0, $summary['limit']);
        $this->assertSame(15000.0, $summary['remaining']);
        $this->assertSame(62.5, $summary['percentage']);
        $this->assertSame('On Track', $summary['status']);
    }

    public function test_budget_status_flips_to_warning_at_80_percent(): void
    {
        $user     = User::factory()->create();
        $category = Category::where('type', 'expense')->first();

        $budget = Budget::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'amount'      => 10000,
        ]);

        Expense::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'amount'      => 8500,
            'date'        => now()->startOfMonth()->addDay(),
        ]);

        $summary = app(BudgetService::class)->summarize($budget);

        $this->assertSame(85.0, $summary['percentage']);
        $this->assertSame('Warning', $summary['status']);
    }

    public function test_budget_status_flips_to_exceeded_at_100_percent(): void
    {
        $user     = User::factory()->create();
        $category = Category::where('type', 'expense')->first();

        $budget = Budget::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'amount'      => 10000,
        ]);

        Expense::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'amount'      => 12000,
            'date'        => now()->startOfMonth()->addDay(),
        ]);

        $summary = app(BudgetService::class)->summarize($budget);

        $this->assertSame(120.0, $summary['percentage']);
        $this->assertSame('Exceeded', $summary['status']);
        $this->assertSame(0.0, $summary['remaining']);
    }

    public function test_budget_ignores_expenses_outside_period(): void
    {
        $user     = User::factory()->create();
        $category = Category::where('type', 'expense')->first();

        $budget = Budget::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'amount'      => 10000,
            'period'      => 'monthly',
        ]);

        // Outside current month
        Expense::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'amount'      => 5000,
            'date'        => now()->subMonths(2)->startOfMonth(),
        ]);

        $summary = app(BudgetService::class)->summarize($budget);

        $this->assertSame(0.0, $summary['spent']);
    }
}