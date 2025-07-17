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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('test_category_id')->constrained('test_categories')->onDelete('cascade');
            $table->enum('type', ['familiarisation', 'sample', 'practice'])->default('familiarisation');
            $table->integer('duration_minutes')->nullable();
            $table->integer('total_questions')->default(0);
            $table->json('instructions')->nullable(); // Test ko'rsatmalari
            $table->json('resources')->nullable(); // Audio files, images, etc.
            $table->boolean('is_active')->default(true);
            $table->boolean('is_timed')->default(false); // Vaqt chegarasi bormi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
