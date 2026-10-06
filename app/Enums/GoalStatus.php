<?php

namespace App\Enums;

enum GoalStatus: string
{
    case Active    = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Active    => 'Active',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Active    => 'bg-primary-subtle text-primary-emphasis',
            self::Completed => 'bg-success-subtle text-success-emphasis',
            self::Cancelled => 'bg-secondary-subtle text-secondary-emphasis',
        };
    }
}