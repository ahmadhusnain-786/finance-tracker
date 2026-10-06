<?php

namespace Database\Factories;

use App\Enums\CategoryType;
use App\Enums\RecurringFrequency;
use App\Models\Category;
use App\Models\Income;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeFactory extends Factory
{
    protected $model = Income::class;

    public function definition(): array
    {
        $isRecurring = $this->faker->boolean(20);

        return [
            'user_id' => User::factory(),
            'category_id' => function () {
                return Category::where('type', CategoryType::Income->value)
                    ->inRandomOrder()
                    ->value('id')
                    ?? Category::factory()->income()->create()->id;
            },
            'payment_method_id' => PaymentMethod::inRandomOrder()->value('id'),
            'title'             => $this->faker->randomElement([
                'Monthly Salary', 'Freelance Project', 'Client Payment',
                'Dividend', 'Rental Income', 'Side Business',
            ]),
            'amount'  => $this->faker->randomFloat(2, 5000, 500000),
            'date'    => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'description' => $this->faker->optional()->sentence(),
            'is_recurring' => $isRecurring,
            'recurring_frequency' => $isRecurring
                ? $this->faker->randomElement(array_column(RecurringFrequency::cases(), 'value'))
                : null,
        ];
    }
}