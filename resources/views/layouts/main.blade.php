<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'IELTS Tayyorgarlik Platformasi')</title>
    <meta name="description" content="@yield('description', 'IELTS imtihoniga tayyorgarlik platformasi. Familiarisation testlar, namuna testlar va tayyorgarlik materiallari.')">
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', 'IELTS Tayyorgarlik Platformasi')">
    <meta property="og:description" content="@yield('description', 'IELTS imtihoniga tayyorgarlik platformasi')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="font-inter antialiased bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-blue-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-2 rounded-lg">
                            <i class="fas fa-graduation-cap text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">IELTS Platform</h1>
                            <p class="text-xs text-gray-500">Tayyorgarlik Markazi</p>
                        </div>
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-home mr-2"></i>Bosh sahifa
                    </a>
                    <a href="{{ route('categories.index') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-folder mr-2"></i>Testlar
                    </a>
                    <a href="#preparation" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-book mr-2"></i>Tayyorgarlik
                    </a>
                    <a href="#resources" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-download mr-2"></i>Resurslar
                    </a>
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-blue-600">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <span class="hidden md:block text-sm font-medium">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-edit mr-2"></i>Profil
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Chiqish
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-sign-in-alt mr-2"></i>Kirish
                        </a>
                        <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            <i class="fas fa-user-plus mr-2"></i>Ro'yxatdan o'tish
                        </a>
                    @endauth
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button x-data @click="$refs.mobileMenu.classList.toggle('hidden')" class="text-gray-700 hover:text-blue-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Navigation -->
        <div x-ref="mobileMenu" class="hidden md:hidden bg-white border-t border-gray-200">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('home') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">
                    <i class="fas fa-home mr-2"></i>Bosh sahifa
                </a>
                <a href="{{ route('categories.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">
                    <i class="fas fa-folder mr-2"></i>Testlar
                </a>
                <a href="#preparation" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">
                    <i class="fas fa-book mr-2"></i>Tayyorgarlik
                </a>
                <a href="#resources" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">
                    <i class="fas fa-download mr-2"></i>Resurslar
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mx-4 mt-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-4 mt-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Platform Info -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-2 rounded-lg">
                            <i class="fas fa-graduation-cap text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">IELTS Platform</h3>
                            <p class="text-gray-400 text-sm">Tayyorgarlik Markazi</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-4">IELTS imtihoniga tayyorgarlik uchun eng yaxshi platforma. Familiarisation testlar, namuna testlar va professional tayyorgarlik materiallari.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-telegram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Tezkor havolalar</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('categories.index') }}" class="text-gray-400 hover:text-white transition-colors">Familiarisation Testlar</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Namuna Testlar</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Tayyorgarlik Materiallari</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Javoblar varaqasi</a></li>
                    </ul>
                </div>
                
                <!-- Support -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Yordam</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Test qo'llanmasi</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Aloqa</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Texnik yordam</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 IELTS Platform. Barcha huquqlar himoyalangan.</p>
            </div>
        </div>
    </footer>
    
    @stack('scripts')
</body>
</html>