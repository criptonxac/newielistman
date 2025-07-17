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
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_test_attempt_id')->constrained('user_test_attempts')->onDelete('cascade');
            $table->foreignId('test_question_id')->constrained('test_questions')->onDelete('cascade');
            $table->text('user_answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->integer('points_earned')->default(0);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_test_attempt_id', 'test_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
