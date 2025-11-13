@extends("admin.layouts.app")

@section("content")
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Tasks</h1>
            <p class="text-gray-600 mt-2">Manage all tasks across projects</p>
        </div>
        <a href="{{ route('tasks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium transition duration-200 flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create New Task
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Professional Mobile Filter Tabs -->
    <div class="lg:hidden mb-6">
        <div class="flex space-x-1 bg-white p-1 rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
            <button data-status="todo" class="task-filter-tab active flex-1 px-4 py-3 text-sm font-medium rounded-lg bg-gray-100 text-gray-800 whitespace-nowrap transition-all duration-200">
                To Do ({{ $tasks->where('status', 'todo')->count() }})
            </button>
            <button data-status="in_progress" class="task-filter-tab flex-1 px-4 py-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                In Progress ({{ $tasks->where('status', 'in_progress')->count() }})
            </button>
            <button data-status="done" class="task-filter-tab flex-1 px-4 py-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                Done ({{ $tasks->where('status', 'done')->count() }})
            </button>
        </div>
    </div>

    <!-- Kanban Board -->
    @if($tasks->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- To Do Column -->
            <div class="task-column active bg-gray-50 rounded-2xl p-6" data-status="todo">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 bg-gray-400 rounded-full mr-3"></div>
                        To Do
                    </h3>
                    <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $tasks->where('status', 'todo')->count() }}
                    </span>
                </div>
                <div class="space-y-4">
                    @foreach($tasks->where('status', 'todo') as $task)
                    @include('admin.tasks.partials.task-card', ['task' => $task, 'status' => 'todo'])
                    @endforeach
                </div>
            </div>

            <!-- In Progress Column -->
            <div class="task-column bg-gray-50 rounded-2xl p-6" data-status="in_progress">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                        In Progress
                    </h3>
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $tasks->where('status', 'in_progress')->count() }}
                    </span>
                </div>
                <div class="space-y-4">
                    @foreach($tasks->where('status', 'in_progress') as $task)
                    @include('admin.tasks.partials.task-card', ['task' => $task, 'status' => 'in_progress'])
                    @endforeach
                </div>
            </div>

            <!-- Done Column -->
            <div class="task-column bg-gray-50 rounded-2xl p-6" data-status="done">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        Done
                    </h3>
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $tasks->where('status', 'done')->count() }}
                    </span>
                </div>
                <div class="space-y-4">
                    @foreach($tasks->where('status', 'done') as $task)
                    @include('admin.tasks.partials.task-card', ['task' => $task, 'status' => 'done'])
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">No tasks found</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">Get started by creating your first task</p>
            <a href="{{ route('tasks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-medium transition duration-200 inline-flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Your First Task
            </a>
        </div>
    @endif
</div>

<script>
// Professional task filter - clean and working
document.addEventListener('DOMContentLoaded', function() {
    const filterTabs = document.querySelectorAll('.task-filter-tab');
    const taskColumns = document.querySelectorAll('.task-column');

    // Initialize mobile view
    function initMobileView() {
        if (window.innerWidth < 1024) {
            taskColumns.forEach((col, index) => {
                if (index === 0) {
                    col.style.display = 'block';
                } else {
                    col.style.display = 'none';
                }
            });
        } else {
            taskColumns.forEach(col => {
                col.style.display = 'block';
            });
        }
    }

    // Filter tab click handler
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const status = this.getAttribute('data-status');

            // Update active tab
            filterTabs.forEach(t => {
                t.classList.remove('active', 'bg-gray-100', 'text-gray-800');
                t.classList.add('text-gray-600', 'hover:bg-gray-100');
            });
            this.classList.remove('text-gray-600', 'hover:bg-gray-100');
            this.classList.add('active', 'bg-gray-100', 'text-gray-800');

            // Show selected column, hide others on mobile
            if (window.innerWidth < 1024) {
                taskColumns.forEach(col => {
                    if (col.getAttribute('data-status') === status) {
                        col.style.display = 'block';
                    } else {
                        col.style.display = 'none';
                    }
                });
            }
        });
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        initMobileView();
    });

    // Initial setup
    initMobileView();
});
</script>

<style>
@media (max-width: 1023px) {
    .task-column {
        display: none;
    }
    .task-column:first-child {
        display: block;
    }
}

.task-filter-tab.active {
    background-color: rgb(243, 244, 246);
    color: rgb(31, 41, 55);
}

.task-filter-tab {
    transition: all 0.2s ease-in-out;
}

.hover\:shadow-md {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
.shadow-sm {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
.rounded-2xl {
    border-radius: 1rem;
}
.rounded-xl {
    border-radius: 0.75rem;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
