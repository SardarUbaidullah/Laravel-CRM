<!-- Alpine.js -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Manager Sidebar -->
<div class="sidebar w-64 flex flex-col h-full"
     x-data="{ open: { projects: false, tasks: false, team: false, reports: false, profile: false } }">

    <!-- Scrollable -->
    <div class="flex-1 overflow-y-auto custom-scrollbar">
        <div class="p-6">
            <!-- Logo -->
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-8 h-8 bg-gradient-to-br from-green-600 to-emerald-500 rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-bold">M</span>
                </div>
                <span class="font-bold text-sidebar-foreground text-lg">Manager Panel</span>
            </div>

            <!-- Menu -->
            <nav class="space-y-1 mb-8">

                <!-- Dashboard -->
                <a href="{{ url('/manager/dashboard') }}"
                   class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium bg-primary text-white border-l-2 border-primary transition-all duration-200">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>

                <!-- My Projects -->
                <div>
                    <button @click="open.projects = !open.projects"
                            class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-briefcase"></i><span>My Projects</span>
                        </div>
                        <i :class="open.projects ? 'fas fa-chevron-up text-xs' : 'fas fa-chevron-down text-xs'"></i>
                    </button>
                    <div x-show="open.projects" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ url('/projects') }}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">All Projects</a>
                        <a href="{{ url('/projects/assigned') }}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Assigned to Me</a>
                        <a href="{{ url('/projects/running') }}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Running Projects</a>
                        <a href="{{ url('/projects/completed') }}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Completed</a>
                    </div>
                </div>

                <!-- Tasks -->
                <div>
                    <button @click="open.tasks = !open.tasks"
                            class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-tasks"></i><span>Tasks</span>
                        </div>
                        <i :class="open.tasks ? 'fas fa-chevron-up text-xs' : 'fas fa-chevron-down text-xs'"></i>
                    </button>
                    <div x-show="open.tasks" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ url('/tasks/create') }}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Create Task</a>
                        <a href="{{ url('/tasks') }}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Manage Tasks</a>
                        <a href="{{ url('/tasks/pending') }}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Pending</a>
                        <a href="{{ url('/tasks/completed') }}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Completed</a>
                    </div>
                </div>

                <!-- Team -->
                <div>
                    <button @click="open.team = !open.team"
                            class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-users"></i><span>My Team</span>
                        </div>
                        <i :class="open.team ? 'fas fa-chevron-up text-xs' : 'fas fa-chevron-down text-xs'"></i>
                    </button>
                    <div x-show="open.team" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ url('/team') }}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Team Members</a>
                        <a href="{{ url('/team/performance') }}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Performance</a>
                    </div>
                </div>

                <!-- Reports -->
                <div>
                    <button @click="open.reports = !open.reports"
                            class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-chart-line"></i><span>Reports</span>
                        </div>
                        <i :class="open.reports ? 'fas fa-chevron-up text-xs' : 'fas fa-chevron-down text-xs'"></i>
                    </button>
                    <div x-show="open.reports" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ url('/reports/project') }}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Project Reports</a>
                        <a href="{{ url('/reports/team') }}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Team Reports</a>
                        <a href="{{ url('/reports/timelog') }}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Time Logs</a>
                    </div>
                </div>

                <!-- Profile -->
                <div>
                    <button @click="open.profile = !open.profile"
                            class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-user-cog"></i><span>My Account</span>
                        </div>
                        <i :class="open.profile ? 'fas fa-chevron-up text-xs' : 'fas fa-chevron-down text-xs'"></i>
                    </button>
                    <div x-show="open.profile" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ url('/profile') }}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Profile</a>
                        <a href="{{ url('/settings') }}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Settings</a>
                    </div>
                </div>

            </nav>
        </div>
    </div>

    <!-- Profile Footer -->
    <div class="p-4 border-t border-sidebar-border">
        <div class="flex items-center space-x-3 p-2 rounded-lg hover:bg-sidebar-accent cursor-pointer">
            <img class="w-8 h-8 rounded-full"
                 src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=32&h=32&fit=crop"
                 alt="User">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-sidebar-foreground truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-muted-foreground capitalize">Manager</p>
            </div>
            <i class="fas fa-chevron-down text-muted-foreground text-xs"></i>
        </div>
    </div>
</div>
