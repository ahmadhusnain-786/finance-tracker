<?php

namespace Database\Factories;

use App\Enums\PaymentMethodType;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'name'        => $this->faker->randomElement(['My Bank', 'My Cash', 'My Wallet']) . ' ' . $this->faker->numberBetween(1, 999),
            'type'        => $this->faker->randomElement(array_column(PaymentMethodType::cases(), 'value')),
            'description' => $this->faker->optional()->sentence(),
            'is_active'   => true,
            'is_system'   => false,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function system(): static
    {
        return $this->state(fn () => [
            'user_id'   => null,
            'is_system' => true,
        ]);
    }
}