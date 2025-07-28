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
        Schema::table('user_test_attempts', function (Blueprint $table) {
            $table->jsonb('answers')->nullable();
            $table->integer('score')->default(0);
            $table->integer('max_score')->default(40);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_test_attempts', function (Blueprint $table) {
            $table->dropColumn(['answers', 'score', 'max_score']);
        });
    }
};
