<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Client Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .sidebar {
            transition: transform 0.3s ease;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
        }
        .active-nav {
            background: #f0f9ff;
            color: #0ea5e9;
            border-right: 3px solid #0ea5e9;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Mobile Menu Button -->
    <button id="menuToggle" class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-white rounded-lg shadow-md">
        <i class="fas fa-bars text-gray-700"></i>
    </button>

    <!-- Mobile Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar fixed lg:static w-64 bg-white border-r border-gray-200 min-h-screen z-40 flex flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-rocket text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Client Portal</h1>
                        <p class="text-sm text-gray-500">Dashboard</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 py-6 px-4">
                <div class="space-y-2">
                    <a href="{{ route('client.dashboard') }}"
                       class="flex items-center space-x-3 py-3 px-4 rounded-lg transition-all duration-200 {{ request()->routeIs('client.dashboard') ? 'active-nav' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <a href="{{ route('client.projects') }}"
                       class="flex items-center space-x-3 py-3 px-4 rounded-lg transition-all duration-200 {{ request()->routeIs('client.projects*') ? 'active-nav' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-briefcase w-5 text-center"></i>
                        <span class="font-medium">Projects</span>
                    </a>
                </div>
            </nav>

            <!-- User Section -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">Client</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors duration-200">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 lg:static">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            @yield('title', 'Dashboard')
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Welcome back, {{ auth()->user()->name }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Search -->
                        <div class="relative hidden md:block">
                            <input type="text" placeholder="Search..."
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-64 text-sm">
                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="fixed bottom-6 right-6 bg-white text-gray-900 px-6 py-4 rounded-lg shadow-xl border-l-4 border-green-500 z-50">
        <div class="flex items-center space-x-3">
            <i class="fas fa-check-circle text-green-500 text-xl"></i>
            <div>
                <p class="font-semibold">Success</p>
                <p class="text-gray-600 text-sm">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="fixed bottom-6 right-6 bg-white text-gray-900 px-6 py-4 rounded-lg shadow-xl border-l-4 border-red-500 z-50">
        <div class="flex items-center space-x-3">
            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
            <div>
                <p class="font-semibold">Error</p>
                <p class="text-gray-600 text-sm">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <script>
        // Mobile menu toggle
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('hidden');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.add('hidden');
        });

        // Close sidebar when clicking on links in mobile
        const navLinks = document.querySelectorAll('nav a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    sidebar.classList.remove('open');
                    overlay.classList.add('hidden');
                }
            });
        });

        // Auto-hide flash messages
        const flashMessages = document.querySelectorAll('[class*="fixed bottom-6"]');
        flashMessages.forEach(message => {
            setTimeout(() => {
                message.style.opacity = '0';
                message.style.transition = 'opacity 0.5s ease';
                setTimeout(() => message.remove(), 500);
            }, 5000);
        });
    </script>
</body>
</html>
