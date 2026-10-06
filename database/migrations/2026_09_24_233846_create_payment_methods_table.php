<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();

            // NULL user_id = system/default payment method
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->string('name', 100);

            $table->enum('type', [
                'cash',
                'bank_account',
                'debit_card',
                'credit_card',
                'digital_wallet',
                'other',
            ])->default('cash');

            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);

            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->unique(['user_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};