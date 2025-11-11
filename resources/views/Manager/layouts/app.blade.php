
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .status-healthy {
            background-color: #19874D;
            color: white;
        }

        .status-warning {
            background-color: #F59522;
            color: white;
        }

        .status-error {
            background-color: #ef4444;
            color: white;
        }

        .status-offline {
            background-color: #D9D9D9;
            color: black;
        }

        .status-active {
            background-color: #19874D;
            color: white;
        }

        .status-inactive {
            background-color: #D9D9D9;
            color: black;
        }

        .bg-primary {
            background-color: #19874D;
        }

        .bg-secondary {
            background-color: #AE9B85;
        }

        .bg-accent {
            background-color: #F59522;
        }

        .bg-muted {
            background-color: #D9D9D9;
        }

        .text-primary {
            color: #19874D;
        }

        .text-secondary {
            color: #AE9B85;
        }

        .text-accent {
            color: #F59522;
        }

        .text-muted {
            color: #6B7280;
        }

        .border-primary {
            border-color: #19874D;
        }

        .border-secondary {
            border-color: #AE9B85;
        }

        .border-accent {
            border-color: #F59522;
        }

        .hover-scale:hover {
            transform: scale(1.02);
        }

        .transition-all-custom {
            transition: all 0.3s ease;
        }

        .scrollbar-custom::-webkit-scrollbar {
            width: 6px;
        }

        .scrollbar-custom::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .scrollbar-custom::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .scrollbar-custom::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .sidebar {
            background-color: #ffffff;
            border-right: 1px solid #e5e7eb;
        }

        .sidebar-foreground {
            color: #111827;
        }

        .sidebar-accent {
            background-color: #f3f4f6;
        }

        .sidebar-border {
            border-color: #e5e7eb;
        }

        .muted-foreground {
            color: #6B7280;
        }

        .border-border {
            border-color: #e5e7eb;
        }

        .bg-card {
            background-color: #ffffff;
        }

        .text-card-foreground {
            color: #111827;
        }

        .bg-accent-hover {
            background-color: #f3f4f6;
        }

        .text-accent-foreground {
            color: #111827;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .slide-in {
            animation: slideIn 0.3s ease-in-out;
        }

        @keyframes slideIn {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
    @include('Manager.layouts.sidebar')


       <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
              <header class="bg-white border-b border-border h-16 px-6 flex items-center justify-between">
                <!-- Left side - Search and Project Selector -->
                <div class="flex items-center space-x-6">
                    <!-- Project Selector -->
                   
                </div>

                <!-- Right side - Icons and Profile -->
                <div class="flex items-center space-x-4">
                    <!-- Icons -->
                    <div class="flex items-center space-x-2">
                        <button class="p-2 text-muted-foreground hover:bg-accent-hover hover:text-accent-foreground rounded-lg transition-colors duration-200">
                          <a href="{{url('/calendar')}}">  <i class="fas fa-calendar"></i></a>
                        </button>

                        <button class="p-2 text-muted-foreground hover:bg-accent-hover hover:text-accent-foreground rounded-lg transition-colors duration-200">
                           <a href="{{url('/chat')}}"><i class="fas fa-comment"></i></a>
                        </button>
                    </div>

                    <!-- Divider -->
                    <div class="w-px h-6 bg-border mx-2"></div>

                    <!-- Profile -->
                        @auth
<div class="relative" x-data="{ open: false }">
    <button
        @click="open = !open"
        class="flex items-center space-x-3 focus:outline-none hover:bg-gray-50 rounded-lg p-2 transition-colors duration-200"
    >
        <img
            class="w-8 h-8 rounded-full object-cover"
            src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=32&h=32&fit=crop&crop=face"
            alt="User Avatar"
        />
        <div class="text-left">
            <p class="text-sm font-medium text-card-foreground">
              {{Auth::user()->name}}
            </p>
            <p class="text-xs text-muted-foreground capitalize">
                Project Manager
            </p>
        </div>
        <svg class="w-4 h-4 text-gray-500 transition-transform duration-200"
             :class="{ 'rotate-180': open }"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
        style="display: none;"
    >
        <!-- User Info -->
        <div class="px-4 py-2 border-b border-gray-100">
            <p class="text-sm font-medium text-gray-900">{{Auth::user()->name}}</p>
            <p class="text-xs text-gray-500">{{Auth::user()->email}}</p>
        </div>

        <!-- Profile Link -->
        <a
            href="{{ route('profile.edit') }}"
            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200"
        >
            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Profile
        </a>

        <!-- Settings Link -->

        <!-- Divider -->
        <div class="border-t border-gray-100 my-1"></div>

        <!-- Logout Form -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200"
            >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Log Out
            </button>
        </form>
    </div>
</div>
                @endauth
                </div>
            </header>
                        <div class="flex-1 p-6 bg-[#FCF8F3] overflow-y-auto">
@yield('content')


 </div>
    </div>

    <!-- User Management Modal -->


    <script>
        // Sample data
        const teamMembers = [
            {
                id: 1,
                name: 'Sarah Kim',
                email: 'sarah@example.com',
                role: 'Project Manager',
                status: 'active',
                lastLogin: '2 hours ago',
                avatar: 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face',
                joinDate: '2023-01-15'
            },
            {
                id: 2,
                name: 'Mike Rodriguez',
                email: 'mike@example.com',
                role: 'Frontend Developer',
                status: 'active',
                lastLogin: '1 hour ago',
                avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&h=150&fit=crop&crop=face',
                joinDate: '2023-02-20'
            },
            {
                id: 3,
                name: 'Alex Chen',
                email: 'alex@example.com',
                role: 'Backend Developer',
                status: 'active',
                lastLogin: '30 minutes ago',
                avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face',
                joinDate: '2023-01-10'
            },
            {
                id: 4,
                name: 'Priya Patel',
                email: 'priya@example.com',
                role: 'UI/UX Designer',
                status: 'inactive',
                lastLogin: '3 days ago',
                avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face',
                joinDate: '2023-03-05'
            }
        ];

        const projects = [
            { id: 1, name: 'Website Redesign', status: 'in_progress' },
            { id: 2, name: 'Mobile App', status: 'in_progress' },
            { id: 3, name: 'E-commerce Platform', status: 'completed' },
            { id: 4, name: 'CRM System', status: 'planning' }
        ];

        const systemMetrics = {
            serverLoad: 45,
            memoryUsage: 68,
            storageUsed: 2.4,
            storageTotal: 10,
            activeSessions: 247,
            apiCalls: 12400,
            uptime: 99.9,
            responseTime: 128,
            errorRate: 0.2
        };

        // DOM Elements
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        const searchInput = document.getElementById('searchInput');
        const clearCacheBtn = document.getElementById('clearCacheBtn');
        const systemAlert = document.getElementById('systemAlert');
        const alertMessage = document.getElementById('alertMessage');
        const usersTableBody = document.getElementById('usersTableBody');
        const addUserBtn = document.getElementById('addUserBtn');
        const userModal = document.getElementById('userModal');
        const modalTitle = document.getElementById('modalTitle');
        const userForm = document.getElementById('userForm');
        const cancelUserBtn = document.getElementById('cancelUserBtn');
        const rebootSystemBtn = document.getElementById('rebootSystemBtn');
        const quickActionBtns = document.querySelectorAll('.quick-action-btn');

        // Current state
        let currentTab = 'overview';
        let editingUserId = null;

        // Initialize the dashboard
        function initDashboard() {
            // Set up tab switching
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tab = button.getAttribute('data-tab');
                    switchTab(tab);
                });
            });

            // Set up search functionality
            searchInput.addEventListener('input', filterUsers);

            // Set up clear cache button
            clearCacheBtn.addEventListener('click', handleClearCache);

            // Set up user management
            addUserBtn.addEventListener('click', () => openUserModal());
            cancelUserBtn.addEventListener('click', closeUserModal);
            userForm.addEventListener('submit', handleUserSubmit);

            // Set up system reboot
            rebootSystemBtn.addEventListener('click', handleSystemReboot);

            // Set up quick actions
            quickActionBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const action = btn.getAttribute('data-action');
                    handleQuickAction(action);
                });
            });

            // Populate users table
            renderUsersTable();

            // Close modal when clicking outside
            userModal.addEventListener('click', (e) => {
                if (e.target === userModal) {
                    closeUserModal();
                }
            });
        }

        // Switch between tabs
        function switchTab(tab) {
            // Update tab buttons
            tabButtons.forEach(button => {
                if (button.getAttribute('data-tab') === tab) {
                    button.classList.remove('text-gray-600', 'hover:text-black', 'hover:bg-gray-100');
                    button.classList.add('bg-primary', 'text-white');
                } else {
                    button.classList.remove('bg-primary', 'text-white');
                    button.classList.add('text-gray-600', 'hover:text-black', 'hover:bg-gray-100');
                }
            });

            // Update tab contents
            tabContents.forEach(content => {
                if (content.id === `${tab}Tab`) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
            });

            currentTab = tab;
        }

        // Filter users based on search
        function filterUsers() {
            const searchTerm = searchInput.value.toLowerCase();
            const filteredUsers = teamMembers.filter(user =>
                user.name.toLowerCase().includes(searchTerm) ||
                user.email.toLowerCase().includes(searchTerm) ||
                user.role.toLowerCase().includes(searchTerm)
            );
            renderUsersTable(filteredUsers);
        }

        // Render users table
        function renderUsersTable(users = teamMembers) {
            usersTableBody.innerHTML = '';

            users.forEach(user => {
                const row = document.createElement('tr');
                row.className = 'border-b border-gray-100 hover:bg-gray-50 transition-colors';
                row.innerHTML = `
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-3">
                            <img
                                src="${user.avatar}"
                                alt="${user.name}"
                                class="w-8 h-8 rounded-full"
                            />
                            <div>
                                <p class="font-medium text-black">${user.name}</p>
                                <p class="text-sm text-gray-500">${user.email}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-secondary text-white">
                            ${user.role}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-black">
                        ${user.lastLogin || 'Never'}
                    </td>
                    <td class="px-4 py-3">
                        <span class="status-badge status-${user.status} inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                            <i class="fas fa-${user.status === 'active' ? 'check-circle' : 'times-circle'} mr-1 text-xs"></i>
                            ${user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end space-x-2">
                            <button
                                onclick="editUser(${user.id})"
                                class="p-1 text-gray-500 hover:text-primary transition-colors"
                                title="Edit User"
                            >
                                <i class="fas fa-edit"></i>
                            </button>
                            <button
                                onclick="deleteUser(${user.id})"
                                class="p-1 text-gray-500 hover:text-red-600 transition-colors"
                                title="Delete User"
                            >
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                `;
                usersTableBody.appendChild(row);
            });
        }

        // Open user modal for creating or editing
        function openUserModal(user = null) {
            if (user) {
                modalTitle.textContent = 'Edit User';
                document.getElementById('userName').value = user.name;
                document.getElementById('userEmail').value = user.email;
                document.getElementById('userRole').value = user.role;
                document.getElementById('userStatus').value = user.status;
                editingUserId = user.id;
            } else {
                modalTitle.textContent = 'Create New User';
                document.getElementById('userName').value = '';
                document.getElementById('userEmail').value = '';
                document.getElementById('userRole').value = 'Developer';
                document.getElementById('userStatus').value = 'active';
                editingUserId = null;
            }
            userModal.classList.remove('hidden');
        }

        // Close user modal
        function closeUserModal() {
            userModal.classList.add('hidden');
            editingUserId = null;
        }

        // Handle user form submission
        function handleUserSubmit(e) {
            e.preventDefault();

            const userData = {
                name: document.getElementById('userName').value,
                email: document.getElementById('userEmail').value,
                role: document.getElementById('userRole').value,
                status: document.getElementById('userStatus').value
            };

            if (editingUserId) {
                // Update existing user
                const userIndex = teamMembers.findIndex(user => user.id === editingUserId);
                if (userIndex !== -1) {
                    teamMembers[userIndex] = { ...teamMembers[userIndex], ...userData };
                    showAlert('User updated successfully!', 'success');
                }
            } else {
                // Create new user
                const newUser = {
                    id: teamMembers.length + 1,
                    ...userData,
                    lastLogin: 'Never',
                    joinDate: new Date().toISOString().split('T')[0],
                    avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face'
                };
                teamMembers.push(newUser);
                showAlert('User created successfully!', 'success');

                // Update stats
                document.getElementById('totalUsers').textContent = teamMembers.length + 1;
            }

            renderUsersTable();
            closeUserModal();
        }

        // Edit user
        function editUser(userId) {
            const user = teamMembers.find(u => u.id === userId);
            if (user) {
                openUserModal(user);
            }
        }

        // Delete user
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                const userIndex = teamMembers.findIndex(user => user.id === userId);
                if (userIndex !== -1) {
                    teamMembers.splice(userIndex, 1);
                    showAlert('User deleted successfully!', 'success');
                    renderUsersTable();

                    // Update stats
                    document.getElementById('totalUsers').textContent = teamMembers.length + 1;
                }
            }
        }

        // Handle clear cache
        function handleClearCache() {
            systemMetrics.serverLoad = Math.max(systemMetrics.serverLoad - 10, 20);
            systemMetrics.memoryUsage = Math.max(systemMetrics.memoryUsage - 15, 40);
            showAlert('Cache cleared successfully!', 'success');
        }

        // Handle system reboot
        function handleSystemReboot() {
            if (confirm('Are you sure you want to reboot the system? This will cause temporary downtime.')) {
                showAlert('System reboot initiated...', 'warning');

                // Simulate reboot process
                setTimeout(() => {
                    showAlert('System reboot completed successfully!', 'success');
                }, 3000);
            }
        }

        // Handle quick actions
        function handleQuickAction(actionId) {
            const messages = {
                'manage-users': 'Switching to users tab',
                'system-settings': 'Opening system settings',
                'security': 'Opening security settings',
                'backup': 'Backup process started...',
                'monitoring': 'Switching to monitoring tab',
                'billing': 'Billing dashboard opened'
            };

            if (actionId === 'manage-users' || actionId === 'monitoring') {
                switchTab(actionId === 'manage-users' ? 'users' : 'monitoring');
            } else {
                showAlert(messages[actionId] || 'Action completed', 'info');
            }
        }

        // Show system alert
        function showAlert(message, type) {
            alertMessage.textContent = message;

            // Update alert styling based on type
            systemAlert.className = `mb-6 p-4 rounded-lg border ${type === 'success' ? 'bg-primary border-primary text-white' :
                type === 'warning' ? 'bg-accent border-accent text-white' :
                'bg-secondary border-secondary text-white'} slide-in`;

            // Update icon based on type
            const icon = systemAlert.querySelector('i');
            icon.className = type === 'success' ? 'fas fa-check-circle' :
                type === 'warning' ? 'fas fa-exclamation-triangle' :
                'fas fa-info-circle';

            systemAlert.classList.remove('hidden');

            // Auto-hide after 5 seconds
            setTimeout(() => {
                hideAlert();
            }, 5000);
        }

        // Hide system alert
        function hideAlert() {
            systemAlert.classList.add('hidden');
        }

        // Initialize the dashboard when the page loads
        document.addEventListener('DOMContentLoaded', initDashboard);
    </script>
</body>
</html>
