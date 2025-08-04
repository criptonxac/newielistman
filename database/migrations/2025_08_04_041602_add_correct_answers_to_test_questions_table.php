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
            if (!Schema::hasColumn('test_questions', 'correct_answers')) {
                $table->json('correct_answers')->nullable()->after('correct_answer');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_questions', function (Blueprint $table) {
            if (Schema::hasColumn('test_questions', 'correct_answers')) {
                $table->dropColumn('correct_answers');
            }
        });
    }
};
