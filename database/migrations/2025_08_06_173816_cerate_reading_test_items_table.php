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
        Schema::create('reading_test_items', function (Blueprint $table) {
            $table->id();
            $table->integer('reading_test_id');
            $table->string('title');
            $table->jsonb('body');
            $table->string('type');
            $table->index('reading_test_id');
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
