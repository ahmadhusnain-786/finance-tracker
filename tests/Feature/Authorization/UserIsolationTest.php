<?php

namespace Tests\Feature\Authorization;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\FinancialGoal;
use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDefaults();
    }

    /**
     * Spec §50: "User A cannot access User B's income."
     */
    public function test_user_cannot_view_another_users_income(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $incomeB = Income::factory()->create(['user_id' => $userB->id]);

        // A tries to open B's income edit page
        $response = $this->actingAs($userA)->get(route('income.edit', $incomeB));

        $response->assertForbidden();
    }

    /**
     * Spec §50: "User A cannot edit User B's expense."
     */
    public function test_user_cannot_update_another_users_expense(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $expenseB = Expense::factory()->create(['user_id' => $userB->id]);

        $response = $this->actingAs($userA)->put(route('expenses.update', $expenseB), [
            'title'       => 'Hacked',
            'amount'      => 9999,
            'category_id' => $expenseB->category_id,
            'date'        => now()->toDateString(),
        ]);

        $response->assertForbidden();

        // Verify B's data was NOT changed
        $this->assertSame($expenseB->fresh()->title, $expenseB->title);
    }

    /**
     * Spec §50: "User A cannot delete User B's transaction."
     */
    public function test_user_cannot_delete_another_users_income(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $incomeB = Income::factory()->create(['user_id' => $userB->id]);

        $response = $this->actingAs($userA)->delete(route('income.destroy', $incomeB));

        $response->assertForbidden();
        $this->assertDatabaseHas('income', ['id' => $incomeB->id]);
    }

    /**
     * Spec §50: "User A cannot access User B's budget."
     */
    public function test_user_cannot_view_another_users_budget(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $budgetB = Budget::factory()->create(['user_id' => $userB->id]);

        $response = $this->actingAs($userA)->get(route('budgets.edit', $budgetB));

        $response->assertForbidden();
    }

    /**
     * Spec §50: "User A cannot access User B's goal."
     */
    public function test_user_cannot_view_another_users_goal(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $goalB = FinancialGoal::factory()->create(['user_id' => $userB->id]);

        $response = $this->actingAs($userA)->get(route('goals.show', $goalB));

        $response->assertForbidden();
    }

    /**
     * The income list page must never show another user's records.
     */
    public function test_income_list_only_shows_own_records(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Income::factory()->create([
            'user_id' => $userA->id,
            'title'   => 'User A Salary',
        ]);

        Income::factory()->create([
            'user_id' => $userB->id,
            'title'   => 'User B Salary',
        ]);

        $response = $this->actingAs($userA)->get(route('income.index'));

        $response->assertOk();
        $response->assertSee('User A Salary');
        $response->assertDontSee('User B Salary');
    }

    /**
     * The dashboard must compute totals only from the logged-in user's records.
     */
    public function test_dashboard_totals_are_per_user(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Income::factory()->create(['user_id' => $userA->id, 'amount' => 1000, 'date' => now()]);
        Income::factory()->create(['user_id' => $userB->id, 'amount' => 9999, 'date' => now()]);

        $response = $this->actingAs($userA)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('1,000.00');
        $response->assertDontSee('9,999.00');
    }
}