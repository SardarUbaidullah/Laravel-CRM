<!-- Alpine.js (add this once in your layout head) -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Sidebar -->
<div class="sidebar w-64 flex flex-col h-full"
    x-data="{
        open: {
            projects: false,
            tasks: false,
            team: false,
            messages: false,
            files: false,
            calendar: false,
            settings: false,
            analytics: false,
            security: false,
        }
    }">

    <!-- Scrollable content -->
    <div class="flex-1 overflow-y-auto custom-scrollbar">
        <div class="p-6">
            <!-- Logo -->
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-8 h-8 bg-gradient-to-br from-primary to-green-600 rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-bold">P</span>
                </div>
                <span class="font-bold text-sidebar-foreground text-lg">Project M.</span>
            </div>

            <!-- Nav Menu -->
            <nav class="space-y-1 mb-8">
                <!-- Dashboard -->
                <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 bg-primary text-white border-l-2 border-primary">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Messages -->
                <div>
                    <button @click="open.messages = !open.messages"
                        class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition-all duration-200">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-comment"></i><span>Messages</span>
                        </div>
                        <i :class="open.messages ? 'fas fa-chevron-up text-xs' : 'fas fa-chevron-down text-xs'"></i>
                    </button>
                    <div x-show="open.messages" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="#" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Inbox</a>
                        <a href="#" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Sent</a>
                        <a href="#" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Compose</a>
                    </div>
                </div>

                <!-- Tasks -->
                <div>
                    <button @click="open.tasks = !open.tasks"
                        class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition-all duration-200">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-check-square"></i><span>Tasks</span>
                        </div>
                        <i :class="open.tasks ? 'fas fa-chevron-up text-xs' : 'fas fa-chevron-down text-xs'"></i>
                    </button>
                    <div x-show="open.tasks" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{url('/tasks/create')}}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Create Task</a>
                        <a href="{{url('/tasks')}}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Manage Tasks</a>
                        <a href="#" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Pending Tasks</a>
                        <a href="#" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Completed Tasks</a>
                    </div>
                </div>

                <!-- Users -->
                <div>
                    <button @click="open.users = !open.users"
                        class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition-all duration-200">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-users"></i><span>Users</span>
                        </div>
                        <i :class="open.users ? 'fas fa-chevron-up text-xs' : 'fas fa-chevron-down text-xs'"></i>
                    </button>
                    <div x-show="open.users" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{url("/users")}}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Manage Users</a>
                        <a href="{{url("/users/create")}}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Add Users</a>
                    </div>
                </div>

                <!-- Projects -->
                <div>
                    <button @click="open.projects = !open.projects"
                        class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition-all duration-200">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-briefcase"></i><span>Projects</span>
                        </div>
                        <i :class="open.projects ? 'fas fa-chevron-up text-xs' : 'fas fa-chevron-down text-xs'"></i>
                    </button>
                    <div x-show="open.projects" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{url("/projects/create")}}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Create Project</a>
                        <a href="{{url("/projects")}}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Manage Projects</a>
                        <a href="" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Running Projects</a>
                        <a href="" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Completed Projects</a>
                    </div>
                </div>

                <!-- Files -->
                <div>
                    <button @click="open.files = !open.files"
                        class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition-all duration-200">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-folder"></i><span>Files</span>
                        </div>
                        <i :class="open.files ? 'fas fa-chevron-up text-xs' : 'fas fa-chevron-down text-xs'"></i>
                    </button>
                    <div x-show="open.files" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{url('/files/create')}}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Upload File</a>
                        <a href="{{url('/files')}}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Manage Files</a>
                        <a href="#" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Shared Files</a>
                    </div>
                </div>

                <!-- Calendar -->
                <div>
                    <button @click="open.calendar = !open.calendar"
                        class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition-all duration-200">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-calendar"></i><span>TimeLogs</span>
                        </div>
                        <i :class="open.calendar ? 'fas fa-chevron-up text-xs' : 'fas fa-chevron-down text-xs'"></i>
                    </button>
                    <div x-show="open.calendar" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{url('/time-logs/create')}}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Add Timelog</a>
                        <a href="{{url('/time-logs')}}" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Manage TimeLogs</a>
                    </div>
                </div>

                <!-- Settings -->
                <div>
                    <button @click="open.settings = !open.settings"
                        class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition-all duration-200">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-cog"></i><span>Settings</span>
                        </div>
                        <i :class="open.settings ? 'fas fa-chevron-up text-xs' : 'fas fa-chevron-down text-xs'"></i>
                    </button>
                    <div x-show="open.settings" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="#" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">General</a>
                        <a href="#" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Account</a>
                        <a href="#" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Security</a>
                    </div>
                </div>

                <!-- Analytics -->
                <div>
                    <button @click="open.analytics = !open.analytics"
                        class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition-all duration-200">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-chart-bar"></i><span>Analytics</span>
                        </div>
                        <i :class="open.analytics ? 'fas fa-chevron-up text-xs' : 'fas fa-chevron-down text-xs'"></i>
                    </button>
                    <div x-show="open.analytics" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="#" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Overview</a>
                        <a href="#" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Reports</a>
                        <a href="#" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Charts</a>
                    </div>
                </div>

                <!-- Security -->
                <div>
                    <button @click="open.security = !open.security"
                        class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition-all duration-200">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-shield-alt"></i><span>Security</span>
                        </div>
                        <i :class="open.security ? 'fas fa-chevron-up text-xs' : 'fas fa-chevron-down text-xs'"></i>
                    </button>
                    <div x-show="open.security" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="#" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">User Access</a>
                        <a href="#" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Roles</a>
                        <a href="#" class="block px-3 py-1 text-sm text-muted-foreground hover:text-sidebar-foreground">Logs</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <!-- User Profile -->
    <div class="p-4 border-t border-sidebar-border">
        <div class="flex items-center space-x-3 p-2 rounded-lg hover:bg-sidebar-accent transition-colors duration-200 cursor-pointer">
            <img class="w-8 h-8 rounded-full"
                src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=32&h=32&fit=crop&crop=face"
                alt="User Name">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-sidebar-foreground truncate">John Doe</p>
                <p class="text-xs text-muted-foreground capitalize">super admin</p>
            </div>
            <i class="fas fa-chevron-down text-muted-foreground text-xs"></i>
        </div>
    </div>
</div>
