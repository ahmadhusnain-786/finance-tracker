<?php

namespace Database\Factories;

use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'user_id'   => User::factory(),
            'name'      => $this->faker->word(),
            'type'      => CategoryType::Expense->value,
            'icon'      => 'bi-tag',
            'color'     => $this->faker->hexColor(),
            'is_system' => false,
        ];
    }

    public function income(): static
    {
        return $this->state(fn () => ['type' => CategoryType::Income->value]);
    }

    public function expense(): static
    {
        return $this->state(fn () => ['type' => CategoryType::Expense->value]);
    }

    public function system(): static
    {
        return $this->state(fn () => [
            'user_id'   => null,
            'is_system' => true,
        ]);
    }
}