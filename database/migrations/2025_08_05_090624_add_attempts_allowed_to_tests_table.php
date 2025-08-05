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
        Schema::table('tests', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('tests', 'attempts_allowed')) {
                $table->integer('attempts_allowed')->default(1)->after('is_active');
            }
            if (!Schema::hasColumn('tests', 'time_limit')) {
                $table->integer('time_limit')->default(30)->after('is_active'); // minutes
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn(['attempts_allowed', 'time_limit']);
        });
    }
};
