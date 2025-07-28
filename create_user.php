<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Student foydalanuvchisini yaratish
$user = User::firstOrCreate(
    ['email' => 'student@ielts.com'],
    [
        'name' => 'Student User',
        'email' => 'student@ielts.com',
        'password' => Hash::make('password'),
        'role' => 'student'
    ]
);

echo "Foydalanuvchi yaratildi: " . $user->name . " (ID: " . $user->id . ")\n";
echo "Email: " . $user->email . "\n";
echo "Parol: password\n";
