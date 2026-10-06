<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->restrictOnDelete();

            $table->foreignId('payment_method_id')
                  ->nullable()
                  ->constrained('payment_methods')
                  ->nullOnDelete();

            $table->enum('type', ['income', 'expense']);
            $table->string('title', 150);
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();

            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'yearly']);

            $table->date('start_date');
            $table->date('end_date')->nullable();

            // The next date this should fire
            $table->date('next_run_date');

            // The last time it actually created a transaction
            $table->date('last_run_date')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index('next_run_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_transactions');
    }
};