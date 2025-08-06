<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('writing_test', function (Blueprint $table) {
            $table->id();
            $table->integer('app_test_id');
            $table->string('title');
            $table->jsonb('questions');
            $table->json('answer');
            $table->index('app_test_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
