<?php

namespace Tests\Feature\Income;

use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncomeCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDefaults();
    }

    private function incomeCategory(): Category
    {
        return Category::where('type', CategoryType::Income->value)->first();
    }

    public function test_user_can_create_income(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('income.store'), [
            'title'       => 'Monthly Salary',
            'amount'      => 300000,
            'category_id' => $this->incomeCategory()->id,
            'date'        => now()->toDateString(),
            'description' => 'September salary',
        ]);

        $response->assertRedirect(route('income.index'));
        $this->assertDatabaseHas('income', [
            'user_id' => $user->id,
            'title'   => 'Monthly Salary',
            'amount'  => 300000,
        ]);
    }

    public function test_amount_must_be_positive(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('income.store'), [
            'title'       => 'Invalid',
            'amount'      => -500,
            'category_id' => $this->incomeCategory()->id,
            'date'        => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_date_cannot_be_in_the_future(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('income.store'), [
            'title'       => 'Future',
            'amount'      => 1000,
            'category_id' => $this->incomeCategory()->id,
            'date'        => now()->addDay()->toDateString(),
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_income_category_must_be_of_income_type(): void
    {
        $user = User::factory()->create();

        $expenseCategory = Category::where('type', CategoryType::Expense->value)->first();

        $response = $this->actingAs($user)->post(route('income.store'), [
            'title'       => 'Wrong type',
            'amount'      => 1000,
            'category_id' => $expenseCategory->id,
            'date'        => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('category_id');
    }

    public function test_user_can_delete_own_income(): void
    {
        $user   = User::factory()->create();
        $income = Income::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('income.destroy', $income));

        $response->assertRedirect(route('income.index'));
        $this->assertDatabaseMissing('income', ['id' => $income->id]);
    }
}