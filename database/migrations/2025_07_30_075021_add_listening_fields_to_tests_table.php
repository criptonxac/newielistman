<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddListeningFieldsToTestsTable extends Migration
{
    public function up()
    {
        // Avval qaysi ustunlar mavjudligini tekshiramiz
        Schema::table('tests', function (Blueprint $table) {
            $columns = Schema::getColumnListing('tests');
            
            if (!in_array('category_id', $columns)) {
                $table->foreignId('category_id')->nullable()->after('id')->constrained();
            }
            if (!in_array('type', $columns)) {
                $table->enum('type', ['practice', 'mock', 'real'])->default('practice')->after('description');
            }
            if (!in_array('duration_minutes', $columns)) {
                $table->integer('duration_minutes')->default(30)->after('type');
            }
            if (!in_array('pass_score', $columns)) {
                $table->integer('pass_score')->default(60)->after('duration_minutes');
            }
            if (!in_array('is_active', $columns)) {
                $table->boolean('is_active')->default(true)->after('pass_score');
            }
            if (!in_array('attempts_allowed', $columns)) {
                $table->integer('attempts_allowed')->default(1)->after('is_active');
            }
        });
    }

    public function down()
    {
        Schema::table('tests', function (Blueprint $table) {
            $columns = Schema::getColumnListing('tests');
            
            if (in_array('category_id', $columns)) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
            if (in_array('type', $columns)) {
                $table->dropColumn('type');
            }
            if (in_array('duration_minutes', $columns)) {
                $table->dropColumn('duration_minutes');
            }
            if (in_array('pass_score', $columns)) {
                $table->dropColumn('pass_score');
            }
            if (in_array('is_active', $columns)) {
                $table->dropColumn('is_active');
            }
            if (in_array('attempts_allowed', $columns)) {
                $table->dropColumn('attempts_allowed');
            }
        });
    }
}