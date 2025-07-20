
@extends('layouts.main')

@section('title', 'Kirish - IELTS Platform')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Logo va sarlavha -->
        <div class="text-center">
            <div class="mx-auto h-20 w-20 bg-blue-600 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-graduation-cap text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">
                Hisobingizga kiring
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                IELTS familiarisation testlarini boshlang
            </p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('status') }}
            </div>
        @endif

        <!-- Login form -->
        <form class="space-y-6" method="POST" action="{{ route('login') }}">
            @csrf
            
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email manzili
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           autocomplete="email" 
                           required 
                           class="appearance-none relative block w-full pl-10 pr-3 py-3 border @error('email') border-red-300 @else border-gray-300 @enderror placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                           placeholder="Email manzilingizni kiriting"
                           value="{{ old('email') }}">
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Parol
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           autocomplete="current-password" 
                           required 
                           class="appearance-none relative block w-full pl-10 pr-3 py-3 border @error('password') border-red-300 @else border-gray-300 @enderror placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                           placeholder="Parolingizni kiriting">
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember me -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" 
                           name="remember" 
                           type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                        Meni eslab qol
                    </label>
                </div>


            </div>

            <!-- Submit button -->
            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-blue-500 group-hover:text-blue-400"></i>
                    </span>
                    Kirish
                </button>
            </div>
        </form>

        <!-- Test hisoblar -->
        <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Test uchun hisoblar:</h3>
            <div class="grid grid-cols-1 gap-3">
                <button onclick="fillLoginForm('admin@ielts.com', 'admin123')" 
                        class="bg-red-100 hover:bg-red-200 border border-red-300 rounded-lg p-3 text-left transition-colors">
                    <div class="font-semibold text-red-800">Admin</div>
                    <div class="text-xs text-red-600">admin@ielts.com / admin123</div>
                </button>
                
                <button onclick="fillLoginForm('teacher@ielts.com', 'teacher123')" 
                        class="bg-green-100 hover:bg-green-200 border border-green-300 rounded-lg p-3 text-left transition-colors">
                    <div class="font-semibold text-green-800">O'qituvchi</div>
                    <div class="text-xs text-green-600">teacher@ielts.com / teacher123</div>
                </button>
                
                <button onclick="fillLoginForm('student@ielts.com', 'student123')" 
                        class="bg-blue-100 hover:bg-blue-200 border border-blue-300 rounded-lg p-3 text-left transition-colors">
                    <div class="font-semibold text-blue-800">Talaba</div>
                    <div class="text-xs text-blue-600">student@ielts.com / student123</div>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function fillLoginForm(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>
@endsection
