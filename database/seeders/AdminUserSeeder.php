<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Default admin user
        User::firstOrCreate(
            ['email' => 'admin@ielts.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true
            ]
        );

        // Default teacher
        User::firstOrCreate(
            ['email' => 'teacher@ielts.com'],
            [
                'name' => 'Demo Teacher',
                'password' => Hash::make('teacher123'),
                'role' => 'teacher',
                'is_active' => true
            ]
        );

        // Default student
        User::firstOrCreate(
            ['email' => 'student@ielts.com'],
            [
                'name' => 'Demo Student',
                'password' => Hash::make('student123'),
                'role' => 'student',
                'is_active' => true
            ]
        );
    }
}
