<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestQuestionsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('test_questions')) {
            Schema::create('test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->onDelete('cascade');
            $table->integer('part_number'); // 1, 2, 3, 4
            $table->integer('question_number');
            $table->text('question_text');
            $table->enum('question_type', [
                'multiple_choice',
                'fill_blank',
                'true_false',
                'matching',
                'map_labeling',
                'table_completion',
                'short_answer'
            ]);
            $table->json('options')->nullable(); // For multiple choice
            $table->string('correct_answer')->nullable();
            $table->json('correct_answers')->nullable(); // For multiple correct answers
            $table->integer('points')->default(1);
            $table->text('explanation')->nullable();
            $table->string('image_path')->nullable(); // For map/diagram questions
            $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('test_questions');
    }
}