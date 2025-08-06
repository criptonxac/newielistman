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
        Schema::table('test_questions', function (Blueprint $table) {
            // Form structure uchun JSON maydon
            $table->json('form_structure')->nullable()->after('options');
            
            // Question context - qo'shimcha matn yoki ma'lumot
            $table->text('question_context')->nullable()->after('form_structure');
            
            // Question format - savol formati (form_completion, simple_fill, etc.)
            $table->string('question_format', 50)->nullable()->after('question_context');
            
            // Drag & Drop uchun sudraladigan elementlar
            $table->json('drag_items')->nullable()->after('question_format');
            
            // Drag & Drop uchun tashlanadigan joylar
            $table->json('drop_zones')->nullable()->after('drag_items');
            
            // Multiple choice uchun harflar (A, B, C, D)
            $table->boolean('show_option_letters')->default(true)->after('drop_zones');
            
            // Essay uchun minimum so'z soni
            $table->integer('min_words')->nullable()->after('show_option_letters');
            
            // Essay uchun maksimum so'z soni
            $table->integer('max_words')->nullable()->after('min_words');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_questions', function (Blueprint $table) {
            $table->dropColumn([
                'form_structure',
                'question_context', 
                'question_format',
                'drag_items',
                'drop_zones',
                'show_option_letters',
                'min_words',
                'max_words'
            ]);
        });
    }
};
