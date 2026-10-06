<?php

namespace Database\Factories;

use App\Enums\CategoryType;
use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    protected $model = Budget::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => function () {
                return Category::where('type', CategoryType::Expense->value)
                    ->inRandomOrder()
                    ->value('id')
                    ?? Category::factory()->expense()->create()->id;
            },
            'amount'    => $this->faker->randomFloat(2, 5000, 100000),
            'period'    => 'monthly',
            'notes'     => $this->faker->optional()->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}