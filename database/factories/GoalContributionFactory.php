<?php

namespace Database\Factories;

use App\Models\FinancialGoal;
use App\Models\GoalContribution;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GoalContributionFactory extends Factory
{
    protected $model = GoalContribution::class;

    public function definition(): array
    {
        return [
            'financial_goal_id' => FinancialGoal::factory(),
            'user_id'           => User::factory(),
            'amount'            => $this->faker->randomFloat(2, 1000, 50000),
            'contribution_date' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'note'              => $this->faker->optional()->sentence(),
        ];
    }
}
