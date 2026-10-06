<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // NULL user_id = system/default category visible to everyone
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->string('name', 100);
            $table->enum('type', ['income', 'expense']);

            // Bootstrap icon name, e.g. "bi-cart"
            $table->string('icon', 50)->nullable();

            // Hex color for charts, e.g. "#2563eb"
            $table->string('color', 7)->nullable();

            $table->boolean('is_system')->default(false);

            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index('is_system');

            // Prevent a user from having two categories with the same name+type
            $table->unique(['user_id', 'name', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};