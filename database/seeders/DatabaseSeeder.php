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
        // Create admin user
        $this->call([
            AdminUserSeeder::class,
        ]);
        // Create admin user with admin@ielts.com
        User::firstOrCreate([
            'email' => 'admin@ielts.com'
        ], [
            'name' => 'Administrator',
            'email' => 'admin@ielts.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'email_verified_at' => now()
        ]);

        // Create demo teacher
        User::firstOrCreate([
            'email' => 'teacher@ielts-platform.com'
        ], [
            'name' => 'Demo Teacher',
            'email' => 'teacher@ielts-platform.com',
            'password' => bcrypt('password'),
            'role' => 'teacher',
            'email_verified_at' => now()
        ]);

        // Create student user
        User::firstOrCreate([
            'email' => 'student@ielts.com'
        ], [
            'name' => 'Demo Student',
            'email' => 'student@ielts.com',
            'password' => bcrypt('student123'),
            'role' => 'student',
            'email_verified_at' => now()
        ]);
        
        // Seed only test categories
        $this->call([
            TestCategorySeeder::class,
        ]);
    }
}
