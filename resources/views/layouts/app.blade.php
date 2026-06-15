<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartWash')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .sidebar-active { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); }
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .mobile-nav { display: none; }
        @media (max-width: 768px) {
            .desktop-sidebar { display: none; }
            .mobile-nav { display: flex; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Mobile Top Bar -->
    <div class="md:hidden fixed top-0 left-0 right-0 bg-white shadow-sm z-50 px-4 py-3 flex items-center justify-between">
        <button onclick="toggleSidebar()" class="text-gray-600">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <h1 class="font-bold text-blue-600 text-lg">CycleWash</h1>
        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
            <i class="fas fa-user text-blue-600 text-sm"></i>
        </div>
    </div>

    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="desktop-sidebar fixed top-0 left-0 h-full w-64 bg-white shadow-lg z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300">
        <div class="p-6 border-b">
    <h1 class="text-2xl font-bold text-blue-600 flex items-center gap-2">
        <img src="{{ asset('logo.png') }}" alt="CycleWash Logo" class="h-14 w-14 object-contain">
        CycleWash
    </h1>
    <p class="text-xs text-gray-400 mt-1">Motor Wash Management</p>
</div>

        <nav class="p-4 space-y-1">
            <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition {{ request()->is('dashboard') ? 'sidebar-active text-white hover:text-white' : '' }}">
                <i class="fas fa-home w-5"></i> <span>Dashboard</span>
            </a>
            <a href="/queue" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition {{ request()->is('queue*') ? 'sidebar-active text-white hover:text-white' : '' }}">
                <i class="fas fa-list-ol w-5"></i> <span>Antrian</span>
            </a>
            <a href="/booking" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition {{ request()->is('booking*') ? 'sidebar-active text-white hover:text-white' : '' }}">
                <i class="fas fa-calendar-check w-5"></i> <span>Booking</span>
            </a>
            <a href="/subscription" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition {{ request()->is('subscription*') ? 'sidebar-active text-white hover:text-white' : '' }}">
                <i class="fas fa-crown w-5"></i> <span>Langganan</span>
            </a>
            <a href="/history" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition {{ request()->is('history*') ? 'sidebar-active text-white hover:text-white' : '' }}">
                <i class="fas fa-history w-5"></i> <span>Riwayat</span>
            </a>
        </nav>

        <div class="absolute bottom-0 left-0 right-0 p-4 border-t">
            <form method="POST" action="/logout">
                @csrf
                <button class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-red-500 hover:bg-red-50 transition">
                    <i class="fas fa-sign-out-alt w-5"></i> <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="md:ml-64 pt-16 md:pt-0 min-h-screen">
        <div class="p-4 md:p-8 fade-in">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-nav fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg z-50 justify-around items-center py-2">
        <a href="/dashboard" class="flex flex-col items-center py-1 px-3 {{ request()->is('dashboard') ? 'text-blue-600' : 'text-gray-400' }}">
            <i class="fas fa-home text-lg"></i>
            <span class="text-xs mt-1">Home</span>
        </a>
        <a href="/queue" class="flex flex-col items-center py-1 px-3 {{ request()->is('queue*') ? 'text-blue-600' : 'text-gray-400' }}">
            <i class="fas fa-list-ol text-lg"></i>
            <span class="text-xs mt-1">Antrian</span>
        </a>
        <a href="/booking" class="flex flex-col items-center py-1 px-3 {{ request()->is('booking*') ? 'text-blue-600' : 'text-gray-400' }}">
            <i class="fas fa-calendar text-lg"></i>
            <span class="text-xs mt-1">Booking</span>
        </a>
        <a href="/subscription" class="flex flex-col items-center py-1 px-3 {{ request()->is('subscription*') ? 'text-blue-600' : 'text-gray-400' }}">
            <i class="fas fa-crown text-lg"></i>
            <span class="text-xs mt-1">Langganan</span>
        </a>
    </nav>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
