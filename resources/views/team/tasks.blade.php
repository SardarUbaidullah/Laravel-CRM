@extends('team.app')

@section('content')
<div class="min-h-screen bg-gray-50/30 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-tasks text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">My Tasks</h1>
                            <p class="text-gray-600 mt-1 flex items-center">
                                <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                All tasks assigned to you across projects
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('team.tasks.index') }}?status=all"
                       class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                              {{ request('status') == 'all' || !request('status') ?
                                'bg-blue-600 text-white shadow-lg shadow-blue-500/25' :
                                'bg-white text-gray-700 border border-gray-300 hover:border-blue-300 hover:shadow-md' }}">
                        <i class="fas fa-layer-group mr-2 text-xs"></i>
                        All Tasks
                        <span class="ml-2 bg-white/20 px-1.5 py-0.5 rounded-full text-xs">
                            {{ $totalCount ?? 0 }}
                        </span>
                    </a>
                    <a href="{{ route('team.tasks.index') }}?status=pending"
                       class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                              {{ request('status') == 'pending' ?
                                'bg-orange-600 text-white shadow-lg shadow-orange-500/25' :
                                'bg-white text-gray-700 border border-gray-300 hover:border-orange-300 hover:shadow-md' }}">
                        <i class="fas fa-clock mr-2 text-xs"></i>
                        Pending
                        <span class="ml-2 bg-white/20 px-1.5 py-0.5 rounded-full text-xs">
                            {{ $pendingCount ?? 0 }}
                        </span>
                    </a>
                    <a href="{{ route('team.tasks.index') }}?status=completed"
                       class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                              {{ request('status') == 'completed' ?
                                'bg-green-600 text-white shadow-lg shadow-green-500/25' :
                                'bg-white text-gray-700 border border-gray-300 hover:border-green-300 hover:shadow-md' }}">
                        <i class="fas fa-check-circle mr-2 text-xs"></i>
                        Completed
                        <span class="ml-2 bg-white/20 px-1.5 py-0.5 rounded-full text-xs">
                            {{ $completedCount ?? 0 }}
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-5 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-1">Total Assigned</p>
                        <p class="text-2xl font-bold">{{ $totalCount ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tasks text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl p-5 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium mb-1">In Progress</p>
                        <p class="text-2xl font-bold">{{ $inProgressCount ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-spinner text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-5 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium mb-1">Completed</p>
                        <p class="text-2xl font-bold">{{ $completedCount ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-5 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium mb-1">Overdue</p>
                        <p class="text-2xl font-bold">{{ $overdueCount ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks List -->
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200/60 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-list-check text-blue-600 mr-3"></i>
                        Task List
                        <span class="ml-2 text-sm text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                            {{ $tasks->total() }} tasks
                        </span>
                    </h2>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <i class="fas fa-filter text-xs"></i>
                        <span>Filtered by: {{ ucfirst(request('status', 'all')) }}</span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if($tasks->count() > 0)
                <div class="space-y-4">
                    @foreach($tasks as $task)
                    @php
                        $dueDate = $task->due_date ? \Carbon\Carbon::parse($task->due_date) : null;
                        $isOverdue = $dueDate && $dueDate->isPast() && $task->status !== 'completed';
                        $isDueSoon = $dueDate && $dueDate->diffInDays(now()) <= 2 && !$isOverdue;
                    @endphp

                    <div class="group p-5 border border-gray-200/60 rounded-xl hover:border-blue-300 hover:shadow-md transition-all duration-200
                                {{ $isOverdue ? 'border-l-4 border-l-red-500 bg-red-50/30' : '' }}
                                {{ $isDueSoon ? 'border-l-4 border-l-orange-500 bg-orange-50/30' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4 flex-1 min-w-0">
                                <!-- Task Icon -->
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform duration-200">
                                    <i class="fas fa-tasks text-blue-600 text-lg"></i>
                                </div>

                                <!-- Task Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-gray-900 text-lg mb-1 truncate group-hover:text-blue-700 transition-colors">
                                                {{ $task->title }}
                                            </h3>
                                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                                {{ $task->description ?? 'No description provided' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Task Meta -->
                                    <div class="flex flex-wrap items-center gap-3 text-sm">
                                        <!-- Project -->
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-blue-100 text-blue-800 text-xs font-medium">
                                            <i class="fas fa-project-diagram mr-1.5 text-xs"></i>
                                            {{ $task->project->name ?? 'No Project' }}
                                        </span>

                                        <!-- Priority -->
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium
                                            @if($task->priority == 'high') bg-red-100 text-red-800
                                            @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            <i class="fas fa-flag mr-1.5 text-xs"></i>
                                            {{ ucfirst($task->priority) }} Priority
                                        </span>

                                        <!-- Due Date -->
                                        @if($dueDate)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium
                                            @if($isOverdue) bg-red-100 text-red-800
                                            @elseif($isDueSoon) bg-orange-100 text-orange-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            <i class="fas fa-calendar-day mr-1.5 text-xs"></i>
                                            Due: {{ $dueDate->format('M d, Y') }}
                                            @if($isOverdue)
                                            <i class="fas fa-exclamation-triangle ml-1.5 text-xs"></i>
                                            @endif
                                        </span>
                                        @endif

                                        <!-- Time Estimate -->
                                        @if($task->estimated_hours)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-purple-100 text-purple-800 text-xs font-medium">
                                            <i class="fas fa-clock mr-1.5 text-xs"></i>
                                            {{ $task->estimated_hours }}h
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Actions & Status -->
                            <div class="flex flex-col items-end space-y-3 ml-4 flex-shrink-0">
                                <!-- Status Badge -->
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium
                                    @if($task->status == 'completed') bg-green-100 text-green-800
                                    @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($task->status == 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    <i class="fas
                                        @if($task->status == 'completed') fa-check-circle
                                        @elseif($task->status == 'in_progress') fa-spinner
                                        @else fa-clock @endif mr-1.5 text-xs">
                                    </i>
                                    {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                </span>

                                <!-- Action Buttons -->
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('team.tasks.show', $task->id) }}"
                                       class="w-8 h-8 bg-blue-100 hover:bg-blue-600 text-blue-600 hover:text-white rounded-lg flex items-center justify-center transition-all duration-200 group/btn"
                                       title="View Task Details">
                                        <i class="fas fa-eye text-xs group-hover/btn:scale-110 transition-transform"></i>
                                    </a>
                                    @if($task->status !== 'completed')
                                    <button class="w-8 h-8 bg-green-100 hover:bg-green-600 text-green-600 hover:text-white rounded-lg flex items-center justify-center transition-all duration-200 group/btn"
                                            title="Mark Complete">
                                        <i class="fas fa-check text-xs group-hover/btn:scale-110 transition-transform"></i>
                                    </button>
                                    @endif
                                </div>

                                <!-- Time Info -->
                                @if($task->created_at)
                                <span class="text-xs text-gray-500 text-right">
                                    Created {{ $task->created_at->diffForHumans() }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($tasks->hasPages())
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ $tasks->firstItem() }} to {{ $tasks->lastItem() }} of {{ $tasks->total() }} results
                        </div>
                        <div class="flex space-x-1">
                            {{ $tasks->links() }}
                        </div>
                    </div>
                </div>
                @endif

                @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tasks text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No tasks found</h3>
                    <p class="text-gray-500 max-w-sm mx-auto mb-6">
                        @if(request('status') == 'completed')
                        You haven't completed any tasks yet. Great work awaits!
                        @elseif(request('status') == 'pending')
                        No pending tasks at the moment. Enjoy the clear schedule!
                        @else
                        No tasks are assigned to you currently.
                        @endif
                    </p>
                    <div class="flex justify-center space-x-3">
                        <a href="{{ route('team.projects') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-colors duration-200">
                            <i class="fas fa-project-diagram mr-2"></i>
                            Browse Projects
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom pagination styling */
.pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
}

.pagination li {
    margin: 0 2px;
}

.pagination li a,
.pagination li span {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 2.5rem;
    height: 2.5rem;
    padding: 0 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
}

.pagination li a:hover {
    background-color: #f3f4f6;
    border-color: #d1d5db;
}

.pagination li.active span {
    background-color: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

.pagination li.disabled span {
    color: #9ca3af;
    background-color: #f9fafb;
    border-color: #e5e7eb;
}
</style>
@endsection
