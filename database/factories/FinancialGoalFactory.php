<?php

namespace Database\Factories;

use App\Enums\GoalStatus;
use App\Models\FinancialGoal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialGoalFactory extends Factory
{
    protected $model = FinancialGoal::class;

    public function definition(): array
    {
        $target = $this->faker->randomFloat(2, 50000, 500000);

        return [
            'user_id'        => User::factory(),
            'name'           => $this->faker->randomElement([
                'New Laptop', 'Emergency Fund', 'Vacation Trip',
                'Car Down Payment', 'House Deposit', 'Wedding Fund',
            ]),
            'target_amount'  => $target,
            'current_amount' => 0,
            'target_date'    => $this->faker->dateTimeBetween('+1 month', '+2 years')->format('Y-m-d'),
            'description'    => $this->faker->optional()->sentence(),
            'status'         => GoalStatus::Active->value,
        ];
    }

    public function completed(): static
    {
        return $this->state(function (array $attrs) {
            return [
                'current_amount' => $attrs['target_amount'] ?? 100000,
                'status'         => GoalStatus::Completed->value,
            ];
        });
    }
}