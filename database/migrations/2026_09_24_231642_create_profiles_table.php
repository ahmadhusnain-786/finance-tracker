<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->string('full_name')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('avatar')->nullable();

            $table->string('currency', 3)->default('PKR');
            $table->string('timezone', 64)->default('Asia/Karachi');
            $table->string('date_format', 20)->default('d/m/Y');

            $table->timestamps();

            $table->index('currency');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};