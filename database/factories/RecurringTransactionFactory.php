<?php

namespace Database\Factories;

use App\Enums\RecurringFrequency;
use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\RecurringTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecurringTransactionFactory extends Factory
{
    protected $model = RecurringTransaction::class;

    public function definition(): array
    {
        $type     = $this->faker->randomElement(['income', 'expense']);
        $start    = $this->faker->dateTimeBetween('-6 months', 'now');

        return [
            'user_id' => User::factory(),
            'category_id' => function () use ($type) {
                return Category::where('type', $type)
                    ->inRandomOrder()
                    ->value('id')
                    ?? Category::factory()->create(['type' => $type])->id;
            },
            'payment_method_id' => PaymentMethod::inRandomOrder()->value('id'),
            'type'              => $type,
            'title'             => $this->faker->randomElement([
                'Monthly Rent', 'Monthly Salary', 'Netflix Subscription',
                'Gym Membership', 'Internet Bill', 'Insurance Premium',
            ]),
            'amount'      => $this->faker->randomFloat(2, 500, 300000),
            'description' => $this->faker->optional()->sentence(),
            'frequency'   => $this->faker->randomElement(array_column(RecurringFrequency::cases(), 'value')),
            'start_date'  => $start->format('Y-m-d'),
            'end_date'    => null,
            'next_run_date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'last_run_date' => null,
            'is_active'     => true,
        ];
    }
}