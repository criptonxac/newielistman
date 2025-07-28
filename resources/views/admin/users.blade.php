<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foydalanuvchilar - Admin Panel</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
<div class="flex bg-gray-100 min-h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-gradient-to-b from-slate-900 via-purple-900 to-slate-900 shadow-2xl">
        <div class="p-6 border-b border-purple-800/30">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                    <i class="fas fa-graduation-cap text-white text-lg"></i>
                </div>
                <span class="ml-3 text-xl font-bold text-white">IELTS Platform</span>
            </div>
        </div>
        
        <nav class="mt-6">
            <div class="px-4 text-xs font-semibold text-purple-300 uppercase tracking-wider mb-3">Dashboard</div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gradient-to-r hover:from-purple-600 hover:to-blue-600 transition-all duration-300">
                <i class="fas fa-home mr-3 text-purple-400"></i>
                Home
            </a>
            
            <div class="px-4 text-xs font-semibold text-purple-300 uppercase tracking-wider mb-3 mt-6">Management</div>
            <a href="{{ route('admin.users') }}" class="flex items-center px-6 py-3 text-white bg-gradient-to-r from-blue-600 to-purple-600 border-r-4 border-blue-400 shadow-lg">
                <i class="fas fa-users mr-3 text-blue-200"></i>
                Users
            </a>
            <a href="{{ route('admin.tests') }}" class="flex items-center px-6 py-3 text-gray-300 hover:text-white hover:bg-gradient-to-r hover:from-purple-600 hover:to-blue-600 transition-all duration-300">
                <i class="fas fa-file-alt mr-3 text-purple-400"></i>
                Tests
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1">
        <!-- Top Header -->
        <div class="bg-white shadow-sm border-b border-gray-200 px-8 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Foydalanuvchilar</h1>
                
                <!-- User Avatar Dropdown -->
                <div class="relative">
                    <button id="userMenuButton" class="flex items-center space-x-3 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500" onclick="toggleUserMenu()">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'Admin' }}&background=6366f1&color=fff" 
                             class="w-8 h-8 rounded-full" alt="Avatar">
                        <div class="text-left">
                            <div class="font-medium text-gray-900">{{ auth()->user()->name ?? 'Administrator' }}</div>
                            <div class="text-xs text-gray-500">Administrator</div>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i>
                            Profil
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog mr-2"></i>
                            Sozlamalar
                        </a>
                        <div class="border-t border-gray-100"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Chiqish
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Content -->
        <div class="p-8">
        
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                <div class="flex">
                    <div class="py-1"><i class="fas fa-check-circle mr-2"></i></div>
                    <div>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <div class="flex">
                    <div class="py-1"><i class="fas fa-exclamation-circle mr-2"></i></div>
                    <div>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Foydalanuvchilar</h2>
                <button onclick="openCreateModal()" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg">
                    <i class="fas fa-plus mr-2"></i>
                    Create User
                </button>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ism</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ro'yxatdan o'tgan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($user->role === 'admin') bg-red-100 text-red-800
                                        @elseif($user->role === 'teacher') bg-green-100 text-green-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('d.m.Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}')" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div id="createModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Yangi Foydalanuvchi Yaratish</h3>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="createUserForm" action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Ism</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Parol (min 8 ta belgi)</label>
                    <input type="password" name="password" required minlength="8" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Rol</label>
                    <select name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Bekor qilish
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-md hover:from-blue-700 hover:to-purple-700">
                        Yaratish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Foydalanuvchini Tahrirlash</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editUserId" name="id">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Ism</label>
                    <input type="text" id="editName" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="editEmail" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Yangi Parol (bo'sh qoldiring agar o'zgartirmoqchi bo'lmasangiz)</label>
                    <input type="password" id="editPassword" name="password" minlength="8" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Rol</label>
                    <select id="editRole" name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Bekor qilish
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-md hover:from-blue-700 hover:to-purple-700">
                        Yangilash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Foydalanuvchini O'chirish</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="deleteMessage"></p>
            </div>
            <div class="flex justify-center space-x-2 mt-4">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Bekor qilish
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        O'chirish
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// User Avatar Dropdown Menu
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const userMenuButton = document.getElementById('userMenuButton');
    const userDropdown = document.getElementById('userDropdown');
    
    if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
        userDropdown.classList.add('hidden');
    }
});

// Create User Modal Functions
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    document.getElementById('createUserForm').reset();
}

// Edit User Modal Functions
function openEditModal(id, name, email, role) {
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editUserId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editRole').value = role;
    document.getElementById('editUserForm').action = `/admin/users/${id}`;
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editUserForm').reset();
}

// Delete User Modal Functions
function confirmDelete(id, name) {
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteMessage').textContent = `Haqiqatan ham "${name}" foydalanuvchisini o'chirmoqchimisiz? Bu amal qaytarib bo'lmaydi.`;
    document.getElementById('deleteForm').action = `/admin/users/${id}`;
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const createModal = document.getElementById('createModal');
    const editModal = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteModal');
    
    if (event.target === createModal) {
        closeCreateModal();
    }
    if (event.target === editModal) {
        closeEditModal();
    }
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
});
</script>
</body>
</html>