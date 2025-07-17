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
        Schema::create('test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('tests')->onDelete('cascade');
            $table->integer('question_number');
            $table->enum('question_type', ['multiple_choice', 'fill_blank', 'essay', 'short_answer', 'true_false', 'matching']);
            $table->text('question_text');
            $table->json('options')->nullable(); // Multiple choice uchun variantlar
            $table->text('correct_answer')->nullable();
            $table->json('acceptable_answers')->nullable(); // Bir nechta to'g'ri javob
            $table->integer('points')->default(1);
            $table->text('explanation')->nullable(); // Javob tushuntirishi
            $table->json('resources')->nullable(); // Audio, rasm va boshqa resurslar
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['test_id', 'question_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_questions');
    }
};
