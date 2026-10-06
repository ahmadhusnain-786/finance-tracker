<?php

namespace Database\Factories;

use App\Enums\CategoryType;
use App\Enums\RecurringFrequency;
use App\Models\Category;
use App\Models\Expense;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        $isRecurring = $this->faker->boolean(20);

        return [
            'user_id' => User::factory(),
            'category_id' => function () {
                return Category::where('type', CategoryType::Expense->value)
                    ->inRandomOrder()
                    ->value('id')
                    ?? Category::factory()->expense()->create()->id;
            },
            'payment_method_id' => PaymentMethod::inRandomOrder()->value('id'),
            'title'             => $this->faker->randomElement([
                'Grocery Shopping', 'Rent Payment', 'Electricity Bill',
                'Fuel', 'Internet Bill', 'Restaurant Dinner',
                'Clothes', 'Medical Checkup',
            ]),
            'amount'  => $this->faker->randomFloat(2, 500, 100000),
            'date'    => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'description' => $this->faker->optional()->sentence(),
            'is_recurring' => $isRecurring,
            'recurring_frequency' => $isRecurring
                ? $this->faker->randomElement(array_column(RecurringFrequency::cases(), 'value'))
                : null,
        ];
    }
}