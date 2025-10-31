@extends('admin.layouts.app')
@section('content')

            <!-- Header -->


            <!-- Dashboard Content -->
                <!-- System Alert -->
                <div id="systemAlert" class="hidden mb-6 p-4 rounded-lg border bg-primary border-primary text-white slide-in">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle"></i>
                            <span class="font-medium" id="alertMessage">User created successfully!</span>
                        </div>
                        <button onclick="hideAlert()" class="hover:opacity-70 transition-opacity">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                </div>

                <!-- Header -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8">
                    <div>
                        <div class="flex items-center space-x-3 mb-2">
                            <i class="fas fa-shield-alt text-primary text-2xl"></i>
                            <h1 class="text-3xl font-bold text-black">System Administration</h1>
                        </div>
                        <p class="text-gray-600">
                            Monitor system health, manage users, and configure platform settings
                        </p>
                    </div>

                    <div class="flex items-center space-x-4 mt-4 lg:mt-0">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input
                                type="text"
                                id="searchInput"
                                placeholder="Search users, logs..."
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary w-64 bg-white"
                            />
                        </div>

                        <button
                            id="clearCacheBtn"
                            class="px-4 py-2 bg-secondary text-white rounded-lg hover:bg-[#9a8874] transition-colors font-medium flex items-center space-x-2"
                        >
                            <i class="fas fa-sync-alt"></i>
                            <span>Clear Cache</span>
                        </button>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <div class="flex space-x-1 bg-white rounded-lg p-1 border border-gray-200 mb-8">
                    <button
                        data-tab="overview"
                        class="tab-btn flex-1 px-4 py-2 rounded-md text-sm font-medium transition-colors bg-primary text-white"
                    >
                        Overview
                    </button>
                    <button
                        data-tab="users"
                        class="tab-btn flex-1 px-4 py-2 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-black hover:bg-gray-100"
                    >
                        Users
                    </button>
                    <button
                        data-tab="system"
                        class="tab-btn flex-1 px-4 py-2 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-black hover:bg-gray-100"
                    >
                        System
                    </button>
                    <button
                        data-tab="monitoring"
                        class="tab-btn flex-1 px-4 py-2 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-black hover:bg-gray-100"
                    >
                        Monitoring
                    </button>
                    <button
                        data-tab="security"
                        class="tab-btn flex-1 px-4 py-2 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-black hover:bg-gray-100"
                    >
                        Security
                    </button>
                </div>

                <!-- Overview Tab -->
                <div id="overviewTab" class="tab-content space-y-8">
                    <!-- System Stats -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                                    <p class="text-2xl font-bold text-black" id="totalUsers">5</p>
                                </div>
                                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-users text-primary text-xl"></i>
                                </div>
                            </div>
                            <div class="flex items-center space-x-1 mt-2">
                                <i class="fas fa-arrow-up text-primary text-xs"></i>
                                <span class="text-xs text-primary">+5 this month</span>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Active Projects</p>
                                    <p class="text-2xl font-bold text-black" id="activeProjects">2</p>
                                </div>
                                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-primary text-xl"></i>
                                </div>
                            </div>
                            <div class="flex items-center space-x-1 mt-2">
                                <i class="fas fa-arrow-up-right text-primary text-xs"></i>
                                <span class="text-xs text-primary">+12% growth</span>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">System Uptime</p>
                                    <p class="text-2xl font-bold text-black" id="systemUptime">99.9%</p>
                                </div>
                                <div class="w-12 h-12 bg-secondary/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-chart-line text-secondary text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="status-badge status-healthy inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    <i class="fas fa-check-circle mr-1 text-xs"></i>
                                    Healthy
                                </span>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Storage Used</p>
                                    <p class="text-2xl font-bold text-black" id="storageUsed">2.4 GB</p>
                                    <p class="text-xs text-gray-500">of 10 GB</p>
                                </div>
                                <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-hdd text-accent text-xl"></i>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div
                                    id="storageBar"
                                    class="h-2 rounded-full bg-accent"
                                    style="width: 24%"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <h2 class="text-xl font-semibold text-black mb-4">Quick Actions</h2>
                        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                            <button
                                data-action="manage-users"
                                class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group"
                            >
                                <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-users text-white text-xl"></i>
                                </div>
                                <span class="text-sm font-medium text-black text-center">
                                    Manage Users
                                </span>
                                <span class="text-xs text-gray-500 text-center mt-1">
                                    User accounts & permissions
                                </span>
                            </button>

                            <button
                                data-action="system-settings"
                                class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group"
                            >
                                <div class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-cog text-white text-xl"></i>
                                </div>
                                <span class="text-sm font-medium text-black text-center">
                                    System Settings
                                </span>
                                <span class="text-xs text-gray-500 text-center mt-1">
                                    Platform configuration
                                </span>
                            </button>

                            <button
                                data-action="security"
                                class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group"
                            >
                                <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-shield-alt text-white text-xl"></i>
                                </div>
                                <span class="text-sm font-medium text-black text-center">
                                    Security
                                </span>
                                <span class="text-xs text-gray-500 text-center mt-1">
                                    Security & access control
                                </span>
                            </button>

                            <button
                                data-action="backup"
                                class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group"
                            >
                                <div class="w-12 h-12 bg-accent rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-database text-white text-xl"></i>
                                </div>
                                <span class="text-sm font-medium text-black text-center">
                                    Backup & Restore
                                </span>
                                <span class="text-xs text-gray-500 text-center mt-1">
                                    Data management
                                </span>
                            </button>

                            <button
                                data-action="monitoring"
                                class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group"
                            >
                                <div class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-chart-line text-white text-xl"></i>
                                </div>
                                <span class="text-sm font-medium text-black text-center">
                                    System Monitoring
                                </span>
                                <span class="text-xs text-gray-500 text-center mt-1">
                                    Real-time metrics
                                </span>
                            </button>

                            <button
                                data-action="billing"
                                class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group"
                            >
                                <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-credit-card text-white text-xl"></i>
                                </div>
                                <span class="text-sm font-medium text-black text-center">
                                    Billing
                                </span>
                                <span class="text-xs text-gray-500 text-center mt-1">
                                    Subscription & payments
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                        <!-- System Health -->
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-semibold text-black">System Health</h3>
                                <span class="status-badge status-healthy inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    <i class="fas fa-check-circle mr-1 text-xs"></i>
                                    Healthy
                                </span>
                            </div>
                            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- Server Health -->
                                <div class="health-metric bg-white rounded-xl p-4 shadow-sm border border-gray-200 hover-scale transition-all-custom">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-server text-primary"></i>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-black">55%</div>
                                            <div class="flex items-center space-x-1 text-xs text-primary">
                                                <i class="fas fa-arrow-up text-xs"></i>
                                                <span>up</span>
                                            </div>
                                        </div>
                                    </div>
                                    <h3 class="text-sm font-medium text-black mb-1">Server Health</h3>
                                    <span class="status-badge status-healthy inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        <i class="fas fa-check-circle mr-1 text-xs"></i>
                                        Healthy
                                    </span>
                                </div>

                                <!-- Database -->
                                <div class="health-metric bg-white rounded-xl p-4 shadow-sm border border-gray-200 hover-scale transition-all-custom">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-database text-primary"></i>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-black">98%</div>
                                            <div class="flex items-center space-x-1 text-xs text-gray-600">
                                                <span>stable</span>
                                            </div>
                                        </div>
                                    </div>
                                    <h3 class="text-sm font-medium text-black mb-1">Database</h3>
                                    <span class="status-badge status-healthy inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        <i class="fas fa-check-circle mr-1 text-xs"></i>
                                        Healthy
                                    </span>
                                </div>

                                <!-- API Performance -->
                                <div class="health-metric bg-white rounded-xl p-4 shadow-sm border border-gray-200 hover-scale transition-all-custom">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-microchip text-primary"></i>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-black">99.8%</div>
                                            <div class="flex items-center space-x-1 text-xs text-primary">
                                                <i class="fas fa-arrow-up text-xs"></i>
                                                <span>up</span>
                                            </div>
                                        </div>
                                    </div>
                                    <h3 class="text-sm font-medium text-black mb-1">API Performance</h3>
                                    <span class="status-badge status-healthy inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        <i class="fas fa-check-circle mr-1 text-xs"></i>
                                        Healthy
                                    </span>
                                </div>

                                <!-- Network -->
                                <div class="health-metric bg-white rounded-xl p-4 shadow-sm border border-gray-200 hover-scale transition-all-custom">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-network-wired text-primary"></i>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-black">99%</div>
                                            <div class="flex items-center space-x-1 text-xs text-primary">
                                                <i class="fas fa-arrow-up text-xs"></i>
                                                <span>up</span>
                                            </div>
                                        </div>
                                    </div>
                                    <h3 class="text-sm font-medium text-black mb-1">Network</h3>
                                    <span class="status-badge status-healthy inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        <i class="fas fa-check-circle mr-1 text-xs"></i>
                                        Healthy
                                    </span>
                                </div>

                                <!-- Storage -->
                                <div class="health-metric bg-white rounded-xl p-4 shadow-sm border border-gray-200 hover-scale transition-all-custom">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-10 h-10 bg-accent/10 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-hdd text-accent"></i>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-black">24%</div>
                                            <div class="flex items-center space-x-1 text-xs text-primary">
                                                <i class="fas fa-arrow-up text-xs"></i>
                                                <span>up</span>
                                            </div>
                                        </div>
                                    </div>
                                    <h3 class="text-sm font-medium text-black mb-1">Storage</h3>
                                    <span class="status-badge status-healthy inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        <i class="fas fa-check-circle mr-1 text-xs"></i>
                                        Healthy
                                    </span>
                                </div>

                                <!-- Security -->
                                <div class="health-metric bg-white rounded-xl p-4 shadow-sm border border-gray-200 hover-scale transition-all-custom">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-shield-alt text-primary"></i>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-black">100%</div>
                                            <div class="flex items-center space-x-1 text-xs text-gray-600">
                                                <span>stable</span>
                                            </div>
                                        </div>
                                    </div>
                                    <h3 class="text-sm font-medium text-black mb-1">Security</h3>
                                    <span class="status-badge status-healthy inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        <i class="fas fa-check-circle mr-1 text-xs"></i>
                                        Healthy
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-semibold text-black">Recent Activity</h3>
                                <button class="text-sm text-primary hover:text-[#146c3e] font-medium">
                                    View All
                                </button>
                            </div>
                            <div class="space-y-2 max-h-96 overflow-y-auto scrollbar-custom">
                                <!-- Activity Item 1 -->
                                <div class="activity-item flex items-start space-x-3 p-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-primary bg-gray-100">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-black">
                                            <span class="font-semibold">Alex Chen</span>
                                            <span class="text-gray-600"> Successful login from New York</span>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">2 minutes ago</p>
                                    </div>
                                </div>

                                <!-- Activity Item 2 -->
                                <div class="activity-item flex items-start space-x-3 p-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-primary bg-gray-100">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-black">
                                            <span class="font-semibold">System</span>
                                            <span class="text-gray-600"> Security patch applied successfully</span>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">15 minutes ago</p>
                                    </div>
                                </div>

                                <!-- Activity Item 3 -->
                                <div class="activity-item flex items-start space-x-3 p-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-accent bg-gray-100">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-black">
                                            <span class="font-semibold">API Gateway</span>
                                            <span class="text-gray-600"> Temporary API slowdown detected</span>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">1 hour ago</p>
                                    </div>
                                </div>

                                <!-- Activity Item 4 -->
                                <div class="activity-item flex items-start space-x-3 p-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-primary bg-gray-100">
                                        <i class="fas fa-database"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-black">
                                            <span class="font-semibold">Backup System</span>
                                            <span class="text-gray-600"> Nightly backup completed successfully</span>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">3 hours ago</p>
                                    </div>
                                </div>

                                <!-- Activity Item 5 -->
                                <div class="activity-item flex items-start space-x-3 p-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-primary bg-gray-100">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-black">
                                            <span class="font-semibold">Sarah Kim</span>
                                            <span class="text-gray-600"> New team member registered</span>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">5 hours ago</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Tab -->
                <div id="usersTab" class="tab-content hidden">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-black">User Management</h2>
                                <button
                                    id="addUserBtn"
                                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-[#146c3e] transition-colors font-medium flex items-center space-x-2"
                                >
                                    <i class="fas fa-user-plus"></i>
                                    <span>Add User</span>
                                </button>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="usersTableBody" class="divide-y divide-gray-100">
                                        <!-- Users will be populated here by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Tab -->
                <div id="systemTab" class="tab-content hidden">
                    <div class="space-y-6">
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                            <h3 class="text-lg font-semibold text-black mb-4">System Configuration</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center p-4 border border-gray-200 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-black">Maintenance Mode</h4>
                                            <p class="text-sm text-gray-600">Temporarily disable platform access</p>
                                        </div>
                                        <button class="px-4 py-2 bg-secondary text-white rounded-lg hover:bg-[#9a8874] transition-colors text-sm">
                                            Enable
                                        </button>
                                    </div>

                                    <div class="flex justify-between items-center p-4 border border-gray-200 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-black">Auto Backups</h4>
                                            <p class="text-sm text-gray-600">Daily at 2:00 AM UTC</p>
                                        </div>
                                        <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-[#146c3e] transition-colors text-sm">
                                            Configure
                                        </button>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex justify-between items-center p-4 border border-gray-200 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-black">Email Notifications</h4>
                                            <p class="text-sm text-gray-600">System alerts and reports</p>
                                        </div>
                                        <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-[#146c3e] transition-colors text-sm">
                                            Enabled
                                        </button>
                                    </div>

                                    <div class="flex justify-between items-center p-4 border border-gray-200 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-black">System Reboot</h4>
                                            <p class="text-sm text-gray-600">Restart all services</p>
                                        </div>
                                        <button
                                            id="rebootSystemBtn"
                                            class="px-4 py-2 bg-accent text-white rounded-lg hover:bg-[#e0861a] transition-colors text-sm"
                                        >
                                            Reboot
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monitoring Tab -->
                <div id="monitoringTab" class="tab-content hidden">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                <h4 class="font-semibold text-black mb-4">API Performance</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Response Time</span>
                                        <span class="font-medium">128ms</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Error Rate</span>
                                        <span class="font-medium">0.2%</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Requests/Min</span>
                                        <span class="font-medium">42</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                <h4 class="font-semibold text-black mb-4">Server Resources</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">CPU Load</span>
                                        <span class="font-medium">45%</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Memory Usage</span>
                                        <span class="font-medium">68%</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Disk I/O</span>
                                        <span class="font-medium">124 MB/s</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                <h4 class="font-semibold text-black mb-4">Database</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Active Connections</span>
                                        <span class="font-medium">24</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Queries/Sec</span>
                                        <span class="font-medium">8.2K</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Cache Hit Rate</span>
                                        <span class="font-medium">94%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Tab -->
                <div id="securityTab" class="tab-content hidden">
                    <div class="space-y-6">
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                            <h3 class="text-lg font-semibold text-black mb-4">Security Settings</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center p-4 border border-gray-200 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-black">Two-Factor Auth</h4>
                                            <p class="text-sm text-gray-600">Require 2FA for all users</p>
                                        </div>
                                        <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-[#146c3e] transition-colors text-sm">
                                            Enforced
                                        </button>
                                    </div>

                                    <div class="flex justify-between items-center p-4 border border-gray-200 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-black">Password Policy</h4>
                                            <p class="text-sm text-gray-600">Strong password requirements</p>
                                        </div>
                                        <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-[#146c3e] transition-colors text-sm">
                                            Configure
                                        </button>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex justify-between items-center p-4 border border-gray-200 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-black">Session Timeout</h4>
                                            <p class="text-sm text-gray-600">Auto-logout after 24 hours</p>
                                        </div>
                                        <button class="px-4 py-2 bg-secondary text-white rounded-lg hover:bg-[#9a8874] transition-colors text-sm">
                                            Edit
                                        </button>
                                    </div>

                                    <div class="flex justify-between items-center p-4 border border-gray-200 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-black">API Rate Limiting</h4>
                                            <p class="text-sm text-gray-600">1000 requests/hour per user</p>
                                        </div>
                                        <button class="px-4 py-2 bg-secondary text-white rounded-lg hover:bg-[#9a8874] transition-colors text-sm">
                                            Adjust
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

@endsection
