<!-- Alpine.js -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Manager Sidebar -->
<div class="sidebar w-64 flex flex-col h-full bg-white border-r border-gray-200"
     x-data="{ open: { projects: false, tasks: false, team: false, reports: false, profile: false } }">

    <!-- Scrollable -->
    <div class="flex-1 overflow-y-auto">
        <div class="p-6">
            <!-- Logo -->
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-10 h-10 bg-gradient-to-br from-green-600 to-emerald-500 rounded-lg flex items-center justify-center shadow-sm">
                    <span class="text-white font-bold text-sm">M</span>
                </div>
                <div>
                    <span class="font-bold text-gray-900 text-lg block">Manager</span>
                    <span class="text-gray-500 text-sm">Panel</span>
                </div>
            </div>

            <!-- Menu -->
            <nav class="space-y-2">

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="flex items-center space-x-3 px-3 py-3 rounded-lg text-sm font-medium bg-green-600 text-white border-l-4 border-green-700">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>

                <!-- My Projects -->
                <div class="border-l-2 border-gray-100 ml-3">
                    <button @click="open.projects = !open.projects"
                            class="flex items-center justify-between w-full px-3 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-briefcase w-5 text-center text-gray-500"></i>
                            <span>My Projects</span>
                        </div>
                        <i :class="open.projects ? 'fas fa-chevron-up text-xs text-gray-400' : 'fas fa-chevron-down text-xs text-gray-400'"></i>
                    </button>
                    <div x-show="open.projects" x-collapse class="ml-6 mt-1 space-y-1">
                        <a href="{{ url('/manager/projects') }}" class="flex items-center justify-between px-3 py-2 rounded text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                            <span>All Projects</span>
                            <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full">
                                {{ \App\Models\Projects::where('manager_id', auth()->id())->count() }}
                            </span>
                        </a>
                        <a href="{{ url('/manager/projects/assigned') }}" class="flex items-center justify-between px-3 py-2 rounded text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                            <span>Assigned to Me</span>
                            <span class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">
                                {{ \App\Models\Projects::where('manager_id', auth()->id())->count() }}
                            </span>
                        </a>
                        <a href="{{ url('/manager/projects/running') }}" class="flex items-center justify-between px-3 py-2 rounded text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                            <span>Running Projects</span>
                            <span class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">
                                {{ \App\Models\Projects::where('manager_id', auth()->id())->whereIn('status', ['pending', 'in_progress'])->count() }}
                            </span>
                        </a>
                        <a href="{{ url('/manager/projects/completed') }}" class="flex items-center justify-between px-3 py-2 rounded text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                            <span>Completed</span>
                            <span class="bg-emerald-100 text-emerald-600 text-xs px-2 py-1 rounded-full">
                                {{ \App\Models\Projects::where('manager_id', auth()->id())->where('status', 'completed')->count() }}
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Tasks -->
                <div class="border-l-2 border-gray-100 ml-3">
                    <button @click="open.tasks = !open.tasks"
                            class="flex items-center justify-between w-full px-3 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-tasks w-5 text-center text-gray-500"></i>
                            <span>Tasks</span>
                        </div>
                        <i :class="open.tasks ? 'fas fa-chevron-up text-xs text-gray-400' : 'fas fa-chevron-down text-xs text-gray-400'"></i>
                    </button>
                    <div x-show="open.tasks" x-collapse class="ml-6 mt-1 space-y-1">
                        <a href="{{ url('/manager/tasks/create') }}" class="block px-3 py-2 rounded text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50">Create Task</a>
                        <a href="{{ url('/manager/tasks') }}" class="flex items-center justify-between px-3 py-2 rounded text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                            <span>Manage Tasks</span>
                            <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full">
                                {{ \App\Models\Tasks::whereHas('project', function($q) { $q->where('manager_id', auth()->id()); })->count() }}
                            </span>
                        </a>
                        <a href="{{ url('/manager/tasks/pending') }}" class="flex items-center justify-between px-3 py-2 rounded text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                            <span>Pending</span>
                            <span class="bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded-full">
                                {{ \App\Models\Tasks::whereHas('project', function($q) { $q->where('manager_id', auth()->id()); })->whereIn('status', ['todo', 'in_progress'])->count() }}
                            </span>
                        </a>
                        <a href="{{ url('/manager/tasks/completed') }}" class="flex items-center justify-between px-3 py-2 rounded text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                            <span>Completed</span>
                            <span class="bg-emerald-100 text-emerald-600 text-xs px-2 py-1 rounded-full">
                                {{ \App\Models\Tasks::whereHas('project', function($q) { $q->where('manager_id', auth()->id()); })->where('status', 'done')->count() }}
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Team -->
                <div class="border-l-2 border-gray-100 ml-3">
                    <button @click="open.team = !open.team"
                            class="flex items-center justify-between w-full px-3 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-users w-5 text-center text-gray-500"></i>
                            <span>My Team</span>
                        </div>
                        <i :class="open.team ? 'fas fa-chevron-up text-xs text-gray-400' : 'fas fa-chevron-down text-xs text-gray-400'"></i>
                    </button>
                    <div x-show="open.team" x-collapse class="ml-6 mt-1 space-y-1">
                        <a href="{{ url('/manager/team') }}" class="flex items-center justify-between px-3 py-2 rounded text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                            <span>Team Members</span>
                            <span class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">
                                {{ \App\Models\User::whereHas('assignedTasks', function($q) {
                                    $q->whereHas('project', function($q2) {
                                        $q2->where('manager_id', auth()->id());
                                    });
                                })->distinct()->count() }}
                            </span>
                        </a>
                        <a href="{{ url('/manager/team/performance') }}" class="block px-3 py-2 rounded text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50">Performance</a>
                    </div>
                </div>

                <!-- Reports -->
                <div class="border-l-2 border-gray-100 ml-3">
                    <button @click="open.reports = !open.reports"
                            class="flex items-center justify-between w-full px-3 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-chart-line w-5 text-center text-gray-500"></i>
                            <span>Reports</span>
                        </div>
                        <i :class="open.reports ? 'fas fa-chevron-up text-xs text-gray-400' : 'fas fa-chevron-down text-xs text-gray-400'"></i>
                    </button>
                    <div x-show="open.reports" x-collapse class="ml-6 mt-1 space-y-1">
                        <a href="" class="block px-3 py-2 rounded text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50">Project Reports</a>
                        <a href="" class="block px-3 py-2 rounded text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50">Team Reports</a>
                        <a href="" class="block px-3 py-2 rounded text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50">Time Logs</a>
                    </div>
                </div>

                <!-- Profile -->
                <div class="border-l-2 border-gray-100 ml-3">
                    <button @click="open.profile = !open.profile"
                            class="flex items-center justify-between w-full px-3 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-user-cog w-5 text-center text-gray-500"></i>
                            <span>My Account</span>
                        </div>
                        <i :class="open.profile ? 'fas fa-chevron-up text-xs text-gray-400' : 'fas fa-chevron-down text-xs text-gray-400'"></i>
                    </button>
                    <div x-show="open.profile" x-collapse class="ml-6 mt-1 space-y-1">
                        <a href="{{ url('/profile') }}" class="block px-3 py-2 rounded text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50">Profile</a>
                        <a href="" class="block px-3 py-2 rounded text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50">Settings</a>
                    </div>
                </div>

            </nav>
        </div>
    </div>

    <!-- Profile Footer -->
    <div class="p-4 border-t border-gray-200 bg-gray-50">
        <div class="flex items-center space-x-3 p-2 rounded-lg">
            <img class="w-10 h-10 rounded-full border-2 border-white shadow-sm"
                 src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face"
                 alt="User">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 capitalize">Manager</p>
            </div>
        </div>
    </div>
</div>
