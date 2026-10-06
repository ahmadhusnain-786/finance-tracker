<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Budget must be on an EXPENSE category (enforced in app logic)
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->cascadeOnDelete();

            // The limit amount for the period, e.g. 40000.00
            $table->decimal('amount', 15, 2);

            // Period type — always 'monthly' for now, but extensible
            $table->enum('period', ['weekly', 'monthly', 'yearly'])
                  ->default('monthly');

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Only one budget per user + category + period
            $table->unique(['user_id', 'category_id', 'period']);

            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};