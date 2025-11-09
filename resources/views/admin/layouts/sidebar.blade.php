<!-- Sidebar -->
<div class="sidebar w-64 flex flex-col h-full bg-sidebar-background border-r border-sidebar-border"
    x-data="{
        open: {
            tasks: false,
            users: false,
            projects: false,
            files: false,
            timelogs: false
        }
    }">

    <!-- Scrollable content -->
    <div class="flex-1 overflow-y-auto custom-scrollbar">
        <div class="p-6">
            <!-- Logo -->
            <div class="flex items-center space-x-3 mb-10">
                <div class="w-10 h-10 bg-gradient-to-br from-primary to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-lg">PB</span>
                </div>
                <div>
                    <span class="font-bold text-sidebar-foreground text-xl">ProjectBase</span>
                    <p class="text-xs text-muted-foreground mt-1">Admin Panel</p>
                </div>
            </div>

            <!-- Navigation Header -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wider px-3 mb-3">Navigation</h3>
            </div>

            <!-- Nav Menu -->
            <nav class="space-y-2">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="flex items-center space-x-4 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 bg-primary text-white shadow-sm group">
                    <div class="w-6 h-6 flex items-center justify-center">
                        <i class="fas fa-home text-sm"></i>
                    </div>
                    <span>Dashboard</span>
                    <div class="ml-auto w-2 h-2 bg-white rounded-full opacity-80"></div>
                </a>

                <!-- Messages -->
                <a href="{{ url('/chat') }}"
                   class="flex items-center space-x-4 px-4 py-3 rounded-xl text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-foreground transition-all duration-200 group">
                    <div class="w-6 h-6 flex items-center justify-center">
                        <i class="fas fa-comment text-muted-foreground group-hover:text-sidebar-foreground"></i>
                    </div>
                    <span>Messages</span>
                    <div class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-chevron-right text-xs text-muted-foreground"></i>
                    </div>
                </a>

                <!-- Tasks -->
                <div class="group">
                    <button @click="open.tasks = !open.tasks"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-6 h-6 flex items-center justify-center">
                                <i class="fas fa-tasks text-muted-foreground group-hover:text-sidebar-foreground"></i>
                            </div>
                            <span>Tasks</span>
                        </div>
                        <i :class="open.tasks ? 'fas fa-chevron-up text-xs text-muted-foreground' : 'fas fa-chevron-down text-xs text-muted-foreground'"></i>
                    </button>
                    <div x-show="open.tasks" x-collapse class="ml-10 mt-2 space-y-2">
                        <a href="{{ url('/tasks/create') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/50 transition-all duration-200">
                            <i class="fas fa-plus text-xs"></i>
                            <span>Create Task</span>
                        </a>
                        <a href="{{ url('/tasks') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/50 transition-all duration-200">
                            <i class="fas fa-list text-xs"></i>
                            <span>Manage Tasks</span>
                        </a>
                    </div>
                </div>

                <!-- Users -->
                <div class="group">
                    <button @click="open.users = !open.users"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-6 h-6 flex items-center justify-center">
                                <i class="fas fa-users text-muted-foreground group-hover:text-sidebar-foreground"></i>
                            </div>
                            <span>Team</span>
                        </div>
                        <i :class="open.users ? 'fas fa-chevron-up text-xs text-muted-foreground' : 'fas fa-chevron-down text-xs text-muted-foreground'"></i>
                    </button>
                    <div x-show="open.users" x-collapse class="ml-10 mt-2 space-y-2">
                        <a href="{{ url('/users') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/50 transition-all duration-200">
                            <i class="fas fa-cog text-xs"></i>
                            <span>Manage Users</span>
                        </a>
                        <a href="{{ url('/users/create') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/50 transition-all duration-200">
                            <i class="fas fa-user-plus text-xs"></i>
                            <span>Add User</span>
                        </a>
                    </div>
                </div>

                <!-- Projects -->
                <div class="group">
                    <button @click="open.projects = !open.projects"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-6 h-6 flex items-center justify-center">
                                <i class="fas fa-briefcase text-muted-foreground group-hover:text-sidebar-foreground"></i>
                            </div>
                            <span>Projects</span>
                        </div>
                        <i :class="open.projects ? 'fas fa-chevron-up text-xs text-muted-foreground' : 'fas fa-chevron-down text-xs text-muted-foreground'"></i>
                    </button>
                    <div x-show="open.projects" x-collapse class="ml-10 mt-2 space-y-2">
                        <a href="{{ url('/projects/create') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/50 transition-all duration-200">
                            <i class="fas fa-plus text-xs"></i>
                            <span>Create Project</span>
                        </a>
                        <a href="{{ url('/projects') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/50 transition-all duration-200">
                            <i class="fas fa-list text-xs"></i>
                            <span>Manage Projects</span>
                        </a>
                    </div>
                </div>

                <!-- Files -->
                <div class="group">
                    <button @click="open.files = !open.files"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-6 h-6 flex items-center justify-center">
                                <i class="fas fa-folder text-muted-foreground group-hover:text-sidebar-foreground"></i>
                            </div>
                            <span>Files</span>
                        </div>
                        <i :class="open.files ? 'fas fa-chevron-up text-xs text-muted-foreground' : 'fas fa-chevron-down text-xs text-muted-foreground'"></i>
                    </button>
                    <div x-show="open.files" x-collapse class="ml-10 mt-2 space-y-2">
                        <a href="{{ url('/files/create') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/50 transition-all duration-200">
                            <i class="fas fa-upload text-xs"></i>
                            <span>Upload File</span>
                        </a>
                        <a href="{{ url('/files') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/50 transition-all duration-200">
                            <i class="fas fa-list text-xs"></i>
                            <span>Manage Files</span>
                        </a>
                    </div>
                </div>

                <!-- TimeLogs -->
                <div class="group">
                    <button @click="open.timelogs = !open.timelogs"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl text-sm font-medium text-sidebar-foreground hover:bg-sidebar-accent transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-6 h-6 flex items-center justify-center">
                                <i class="fas fa-clock text-muted-foreground group-hover:text-sidebar-foreground"></i>
                            </div>
                            <span>TimeLogs</span>
                        </div>
                        <i :class="open.timelogs ? 'fas fa-chevron-up text-xs text-muted-foreground' : 'fas fa-chevron-down text-xs text-muted-foreground'"></i>
                    </button>
                    <div x-show="open.timelogs" x-collapse class="ml-10 mt-2 space-y-2">
                        <a href="{{ url('/time-logs/create') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/50 transition-all duration-200">
                            <i class="fas fa-plus text-xs"></i>
                            <span>Add Timelog</span>
                        </a>
                        <a href="{{ url('/time-logs') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm text-muted-foreground hover:text-sidebar-foreground hover:bg-white/50 transition-all duration-200">
                            <i class="fas fa-list text-xs"></i>
                            <span>Manage TimeLogs</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <!-- User Profile -->
    <div class="p-6 border-t border-sidebar-border bg-white/50">
        <div class="flex items-center space-x-3 p-3 rounded-xl bg-sidebar-accent hover:bg-sidebar-accent/80 transition-all duration-200 cursor-pointer group">
            <div class="w-10 h-10 bg-gradient-to-br from-primary to-green-600 rounded-xl flex items-center justify-center text-white font-semibold shadow-sm">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-sidebar-foreground truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-muted-foreground capitalize">{{ Auth::user()->role }}</p>
            </div>
            <i class="fas fa-chevron-down text-muted-foreground text-xs group-hover:text-sidebar-foreground"></i>
        </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
