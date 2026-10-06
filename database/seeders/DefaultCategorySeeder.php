<?php

namespace Database\Seeders;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DefaultCategorySeeder extends Seeder
{
    public function run(): void
    {
        $income = [
            ['name' => 'Salary',     'icon' => 'bi-cash-stack',    'color' => '#16a34a'],
            ['name' => 'Freelance',  'icon' => 'bi-laptop',        'color' => '#22c55e'],
            ['name' => 'Business',   'icon' => 'bi-briefcase',     'color' => '#4ade80'],
            ['name' => 'Investment', 'icon' => 'bi-graph-up',      'color' => '#15803d'],
            ['name' => 'Rental',     'icon' => 'bi-house',         'color' => '#65a30d'],
            ['name' => 'Bonus',      'icon' => 'bi-gift',          'color' => '#84cc16'],
            ['name' => 'Gift',       'icon' => 'bi-gift-fill',     'color' => '#a3e635'],
            ['name' => 'Other',      'icon' => 'bi-three-dots',    'color' => '#94a3b8'],
        ];

        $expenses = [
            ['name' => 'Food',          'icon' => 'bi-egg-fried',       'color' => '#dc2626'],
            ['name' => 'Rent',          'icon' => 'bi-house-door',      'color' => '#b91c1c'],
            ['name' => 'Utilities',     'icon' => 'bi-lightning-charge', 'color' => '#ea580c'],
            ['name' => 'Transport',     'icon' => 'bi-bus-front',       'color' => '#f59e0b'],
            ['name' => 'Shopping',      'icon' => 'bi-bag',             'color' => '#eab308'],
            ['name' => 'Healthcare',    'icon' => 'bi-heart-pulse',     'color' => '#ef4444'],
            ['name' => 'Education',     'icon' => 'bi-book',            'color' => '#8b5cf6'],
            ['name' => 'Entertainment', 'icon' => 'bi-controller',      'color' => '#a855f7'],
            ['name' => 'Travel',        'icon' => 'bi-airplane',        'color' => '#ec4899'],
            ['name' => 'Insurance',     'icon' => 'bi-shield-check',    'color' => '#0ea5e9'],
            ['name' => 'Bills',         'icon' => 'bi-receipt',         'color' => '#0891b2'],
            ['name' => 'Other',         'icon' => 'bi-three-dots',      'color' => '#94a3b8'],
        ];

        foreach ($income as $cat) {
            Category::updateOrCreate(
                ['user_id' => null, 'name' => $cat['name'], 'type' => CategoryType::Income->value],
                array_merge($cat, [
                    'type'      => CategoryType::Income->value,
                    'is_system' => true,
                ])
            );
        }

        foreach ($expenses as $cat) {
            Category::updateOrCreate(
                ['user_id' => null, 'name' => $cat['name'], 'type' => CategoryType::Expense->value],
                array_merge($cat, [
                    'type'      => CategoryType::Expense->value,
                    'is_system' => true,
                ])
            );
        }
    }
}