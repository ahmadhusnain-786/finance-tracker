<?php

namespace App\Enums;

enum PaymentMethodType: string
{
    case Cash           = 'cash';
    case BankAccount    = 'bank_account';
    case DebitCard      = 'debit_card';
    case CreditCard     = 'credit_card';
    case DigitalWallet  = 'digital_wallet';
    case Other          = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash          => 'Cash',
            self::BankAccount   => 'Bank Account',
            self::DebitCard     => 'Debit Card',
            self::CreditCard    => 'Credit Card',
            self::DigitalWallet => 'Digital Wallet',
            self::Other         => 'Other',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Cash          => 'bi-cash',
            self::BankAccount   => 'bi-bank',
            self::DebitCard     => 'bi-credit-card',
            self::CreditCard    => 'bi-credit-card-2-front',
            self::DigitalWallet => 'bi-wallet2',
            self::Other         => 'bi-three-dots',
        };
    }
}