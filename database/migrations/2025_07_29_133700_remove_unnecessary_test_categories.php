<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\TestCategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Faqat kerakli kategoriyalarni qoldirish
        $requiredCategories = ['Listening', 'Academic Reading', 'Academic Writing'];
        
        // Kerak bo'lmagan kategoriyalarni o'chirish
        TestCategory::whereNotIn('name', $requiredCategories)->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Bu migrationni orqaga qaytarib bo'lmaydi
    }
};
