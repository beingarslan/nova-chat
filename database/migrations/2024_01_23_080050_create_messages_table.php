<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('from_id');
            $table->foreignId('to_id');
            $table->text('body')->nullable();
            $table->dateTime('seen_at')->nullable();

            $table->nullableTimestamps();

            $table->foreign('from_id')
                ->on('users')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('to_id')
                ->on('users')
                ->references('id')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
