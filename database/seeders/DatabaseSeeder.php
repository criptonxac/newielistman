<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        User::factory()->create([
            'name' => 'IELTS Admin',
            'email' => 'admin@ielts-platform.com',
        ]);

        // Seed test data
        $this->call([
            TestCategorySeeder::class,
            TestSeeder::class,
            TestQuestionSeeder::class,
        ]);
    }
}
