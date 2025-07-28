<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Foydalanuvchi rollariga qarab dashboard ko'rsatish
     */
    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        switch ($user->role) {
            case UserRole::ADMIN->value:
                return redirect()->route('admin.dashboard');
            case UserRole::TEACHER->value:
                return redirect()->route('teacher.dashboard');
            case UserRole::STUDENT->value:
                return redirect()->route('student.dashboard');
            default:
                return redirect()->route('home');
        }
    }

    /**
     * Admin uchun to'g'ridan-to'g'ri kirish (faqat development uchun)
     */
    public function adminDirect()
    {
        // Create temporary admin session
        $adminUser = \App\Models\User::where('role', UserRole::ADMIN->value)->first();
        
        if (!$adminUser) {
            // Create admin user if not exists
            $adminUser = \App\Models\User::create([
                'name' => 'Admin User',
                'email' => 'admin@ielts.com',
                'password' => bcrypt('admin123'),
                'role' => UserRole::ADMIN->value,
            ]);
        }

        auth()->login($adminUser);
        
        return redirect()->route('admin.dashboard');
    }
}
