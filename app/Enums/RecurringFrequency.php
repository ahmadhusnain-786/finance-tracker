<?php

namespace App\Enums;

enum RecurringFrequency: string
{
    case Daily   = 'daily';
    case Weekly  = 'weekly';
    case Monthly = 'monthly';
    case Yearly  = 'yearly';

    public function label(): string
    {
        return match ($this) {
            self::Daily   => 'Daily',
            self::Weekly  => 'Weekly',
            self::Monthly => 'Monthly',
            self::Yearly  => 'Yearly',
        };
    }

    /**
     * Number of days this frequency represents (approx for month/year).
     * Used later for forecast charts.
     */
    public function approximateDays(): int
    {
        return match ($this) {
            self::Daily   => 1,
            self::Weekly  => 7,
            self::Monthly => 30,
            self::Yearly  => 365,
        };
    }
}