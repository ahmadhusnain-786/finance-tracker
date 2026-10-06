<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
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

            $table->string('title', 150);

            // Always stored as POSITIVE. Type of the row (expense) implies sign.
            $table->decimal('amount', 15, 2);

            $table->date('date');

            $table->text('description')->nullable();

            $table->boolean('is_recurring')->default(false);

            $table->enum('recurring_frequency', [
                'daily', 'weekly', 'monthly', 'yearly',
            ])->nullable();

            $table->timestamps();

            $table->index(['user_id', 'date']);
            $table->index(['user_id', 'category_id']);
            $table->index(['user_id', 'is_recurring']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};