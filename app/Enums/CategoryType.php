<?php

namespace App\Enums;

enum CategoryType: string
{
    case Income  = 'income';
    case Expense = 'expense';

    public function label(): string
    {
        return match ($this) {
            self::Income  => 'Income',
            self::Expense => 'Expense',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Income  => 'bg-success-subtle text-success-emphasis',
            self::Expense => 'bg-danger-subtle text-danger-emphasis',
        };
    }
}