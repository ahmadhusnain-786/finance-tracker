<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goal_contributions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('financial_goal_id')
                  ->constrained('financial_goals')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->decimal('amount', 15, 2);
            $table->date('contribution_date');
            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['financial_goal_id', 'contribution_date']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goal_contributions');
    }
};