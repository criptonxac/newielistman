<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Avval mavjud check constraint-ni o'chirish
        DB::statement('ALTER TABLE test_questions DROP CONSTRAINT IF EXISTS test_questions_question_type_check');
        
        // Yangi check constraint qo'shish - faqat 5 ta savol turi
        DB::statement("ALTER TABLE test_questions ADD CONSTRAINT test_questions_question_type_check CHECK (question_type::text = ANY (ARRAY['multiple_choice'::character varying, 'fill_blank'::character varying, 'true_false'::character varying, 'drag_drop'::character varying, 'essay'::character varying]::text[]))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Avval mavjud check constraint-ni o'chirish
        DB::statement('ALTER TABLE test_questions DROP CONSTRAINT IF EXISTS test_questions_question_type_check');
        
        // Eski constraint-ni qaytarish
        DB::statement("ALTER TABLE test_questions ADD CONSTRAINT test_questions_question_type_check CHECK (question_type::text = ANY (ARRAY['multiple_choice'::character varying, 'fill_blank'::character varying, 'essay'::character varying, 'short_answer'::character varying, 'true_false'::character varying, 'matching'::character varying, 'multiple_answer'::character varying, 'drag_drop'::character varying]::text[]))");
    }
};
