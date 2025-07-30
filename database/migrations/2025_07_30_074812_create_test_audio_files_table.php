<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestAudioFilesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('test_audio_files')) {
            Schema::create('test_audio_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('duration_seconds')->nullable();
            $table->integer('part_number'); // Part 1, 2, 3, 4
            $table->integer('play_order')->default(1);
            $table->boolean('auto_play')->default(true);
            $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('test_audio_files');
    }
}