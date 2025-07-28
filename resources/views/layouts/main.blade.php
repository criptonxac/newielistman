<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IELTS Platform')</title>
    <meta name="description" content="@yield('description', 'IELTS familiarisation testlari va mashqlar platformasi')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fallback CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/test-interface.css') }}" rel="stylesheet">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50">
    <!-- Header Navigation -->
    <header class="bg-white shadow-sm">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and Brand -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}" class="flex items-center">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-graduation-cap text-white text-lg"></i>
                            </div>
                            <span class="text-xl font-bold text-gray-900">IELTS Platform</span>
                        </a>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('categories.index') }}" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                        <i class="fas fa-list-ul mr-1"></i> Kategoriyalar
                    </a>
                    <a href="{{ route('student.tests') }}" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                        <i class="fas fa-file-alt mr-1"></i> Barcha Testlar
                    </a>
                    <a href="{{ route('student.results') }}" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                        <i class="fas fa-chart-line mr-1"></i> Natijalar
                    </a>
                </div>

                <!-- Auth Links -->
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="relative">
                            <button id="userMenuButton" class="flex items-center space-x-3 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500" onclick="toggleUserMenu()">
                                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'Student' }}&background=10b981&color=fff" 
                                     class="w-8 h-8 rounded-full" alt="Avatar">
                                <div class="text-left">
                                    <div class="font-medium text-gray-900">{{ auth()->user()->name ?? 'Talaba' }}</div>
                                    <div class="text-xs text-gray-500">Talaba</div>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                                <a href="{{ route('student.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>
                                    Profil
                                </a>
                                <a href="{{ route('student.results') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Natijalarim
                                </a>
                                <a href="{{ route('categories.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-play mr-2"></i>
                                    Test Boshlash
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        Chiqish
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                            Kirish
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button type="button" class="text-gray-600 hover:text-blue-600 p-2" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="hidden md:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-gray-50">
                    <a href="{{ route('categories.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-200 hover:text-blue-700">
                        <i class="fas fa-list-ul mr-2"></i> Kategoriyalar
                    </a>
                    <a href="{{ route('student.tests') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-200 hover:text-blue-700">
                        <i class="fas fa-file-alt mr-2"></i> Barcha Testlar
                    </a>
                    <a href="{{ route('student.results') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-200 hover:text-blue-700">
                        <i class="fas fa-chart-line mr-2"></i> Natijalar
                    </a>
                    
                    <!-- Development Admin Access -->
                    <a href="{{ route('admin.direct') }}" 
                       class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        Admin Panel
                    </a>
                    <a href="{{ route('categories.index') }}" class="text-gray-600 hover:text-blue-600 block px-3 py-2 text-base font-medium transition-colors">
                        Testlar
                    </a>
                    <a href="{{ route('about') }}" class="text-gray-600 hover:text-blue-600 block px-3 py-2 text-base font-medium transition-colors">
                        Haqida
                    </a>
                    <a href="{{ route('help') }}" class="text-gray-600 hover:text-blue-600 block px-3 py-2 text-base font-medium transition-colors">
                        Yordam
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-600 block px-3 py-2 text-base font-medium transition-colors">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="px-3 py-2">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-red-600 text-base font-medium transition-colors">
                                Chiqish
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 block px-3 py-2 text-base font-medium transition-colors">
                            Kirish
                        </a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Mobile Menu Script -->
    <script>
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        }
        
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }
        
        // Close the dropdown when clicking outside
        window.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const userMenuButton = document.getElementById('userMenuButton');
            
            if (!dropdown.classList.contains('hidden') && 
                !userMenuButton.contains(event.target) && 
                !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>