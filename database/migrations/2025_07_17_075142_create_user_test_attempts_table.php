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
        Schema::create('user_test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('test_id')->constrained('tests')->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('total_score')->nullable();
            $table->integer('total_questions');
            $table->integer('correct_answers')->default(0);
            $table->json('results')->nullable(); // Batafsil natijalar
            $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
            $table->string('session_id')->nullable(); // Guest users uchun
            $table->timestamps();
            
            $table->index(['user_id', 'test_id']);
            $table->index(['session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_test_attempts');
    }
};
