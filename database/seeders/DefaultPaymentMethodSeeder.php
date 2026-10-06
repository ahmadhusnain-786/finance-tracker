<?php

namespace Database\Seeders;

use App\Enums\PaymentMethodType;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class DefaultPaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['name' => 'Cash',           'type' => PaymentMethodType::Cash,          'description' => 'Physical cash payments.'],
            ['name' => 'Bank Account',   'type' => PaymentMethodType::BankAccount,   'description' => 'Direct bank transfers.'],
            ['name' => 'Debit Card',     'type' => PaymentMethodType::DebitCard,     'description' => 'Debit card payments.'],
            ['name' => 'Credit Card',    'type' => PaymentMethodType::CreditCard,    'description' => 'Credit card payments.'],
            ['name' => 'Digital Wallet', 'type' => PaymentMethodType::DigitalWallet, 'description' => 'Online wallets like Easypaisa, JazzCash, PayPal.'],
            ['name' => 'Other',          'type' => PaymentMethodType::Other,         'description' => 'Other payment types.'],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['user_id' => null, 'name' => $method['name']],
                [
                    'type'        => $method['type']->value,
                    'description' => $method['description'],
                    'is_active'   => true,
                    'is_system'   => true,
                ]
            );
        }
    }
}