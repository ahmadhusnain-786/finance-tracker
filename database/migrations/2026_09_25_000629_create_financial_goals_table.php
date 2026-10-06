<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_goals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->string('name', 150);
            $table->decimal('target_amount', 15, 2);

            // Cached sum of contributions; recalculated on each contribution event
            $table->decimal('current_amount', 15, 2)->default(0);

            $table->date('target_date')->nullable();
            $table->text('description')->nullable();

            $table->enum('status', ['active', 'completed', 'cancelled'])
                  ->default('active');

            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_goals');
    }
};