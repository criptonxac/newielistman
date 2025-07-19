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
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                        Bosh sahifa
                    </a>
                    <a href="{{ route('categories.index') }}" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                        Testlar
                    </a>
                    <a href="{{ route('about') }}" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                        Haqida
                    </a>
                    <a href="{{ route('help') }}" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                        Yordam
                    </a>
                </div>

                <!-- Auth Links -->
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-700">Salom, {{ Auth::user()->name }}!</span>
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                                Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors">
                                    Chiqish
                                </button>
                            </form>
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
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-blue-600 block px-3 py-2 text-base font-medium transition-colors">
                        Bosh sahifa
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
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>