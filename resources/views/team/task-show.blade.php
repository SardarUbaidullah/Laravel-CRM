@extends('team.app')

@section('content')
<div class="min-h-screen bg-gray-50/30 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <div class="flex items-center space-x-3 mb-4">
                        <a href="{{ route('team.tasks.index') }}"
                           class="group w-10 h-10 bg-white border border-gray-300 rounded-xl flex items-center justify-center text-gray-600 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all duration-200">
                            <i class="fas fa-arrow-left group-hover:scale-110 transition-transform"></i>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">{{ $task->title }}</h1>
                            <p class="text-gray-600 mt-1 flex items-center">
                                <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                Task details and management
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Status & Priority Badges -->
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium
                        @if($task->status == 'completed') bg-green-100 text-green-800 border border-green-200
                        @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800 border border-blue-200
                        @elseif($task->status == 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                        @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                        <i class="fas
                            @if($task->status == 'completed') fa-check-circle
                            @elseif($task->status == 'in_progress') fa-spinner
                            @else fa-clock @endif mr-2 text-xs">
                        </i>
                        {{ str_replace('_', ' ', ucfirst($task->status)) }}
                    </span>
                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium
                        @if($task->priority == 'high') bg-red-100 text-red-800 border border-red-200
                        @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800 border border-yellow-200
                        @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                        <i class="fas fa-flag mr-2 text-xs"></i>
                        {{ ucfirst($task->priority) }} Priority
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Main Content - Task Details -->
            <div class="xl:col-span-2 space-y-6">
                <!-- Task Information Card -->
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200/60 bg-gradient-to-r from-gray-50 to-white">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                            Task Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <!-- Description Section -->
                        @if($task->description)
                        <div class="mb-8">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4 flex items-center">
                                <i class="fas fa-align-left text-gray-400 mr-2 text-xs"></i>
                                Description
                            </h3>
                            <div class="bg-gray-50/50 border border-gray-200/60 rounded-xl p-5">
                                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $task->description }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Task Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Project Info -->
                            <div class="space-y-4">
                                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center">
                                    <i class="fas fa-project-diagram text-blue-500 mr-2 text-xs"></i>
                                    Project Details
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-3 bg-blue-50/50 rounded-lg border border-blue-200/60">
                                        <span class="text-sm font-medium text-gray-700">Project Name</span>
                                        <span class="text-sm font-semibold text-blue-700">{{ $task->project->name }}</span>
                                    </div>
                                    @if($task->project->manager)
                                    <div class="flex items-center justify-between p-3 bg-gray-50/50 rounded-lg border border-gray-200/60">
                                        <span class="text-sm font-medium text-gray-700">Project Manager</span>
                                        <span class="text-sm text-gray-900">{{ $task->project->manager->name }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Timeline & Status -->
                            <div class="space-y-4">
                                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center">
                                    <i class="fas fa-calendar-alt text-green-500 mr-2 text-xs"></i>
                                    Timeline & Status
                                </h3>
                                <div class="space-y-3">
                                    @if($task->due_date)
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($task->due_date);
                                        $isOverdue = $dueDate->isPast() && $task->status != 'completed';
                                        $isDueSoon = $dueDate->diffInDays(now()) <= 2 && !$isOverdue;
                                    @endphp
                                    <div class="flex items-center justify-between p-3 rounded-lg border
                                        @if($isOverdue) bg-red-50/50 border-red-200/60
                                        @elseif($isDueSoon) bg-orange-50/50 border-orange-200/60
                                        @else bg-gray-50/50 border-gray-200/60 @endif">
                                        <span class="text-sm font-medium text-gray-700">Due Date</span>
                                        <div class="text-right">
                                            <span class="text-sm font-semibold
                                                @if($isOverdue) text-red-700
                                                @elseif($isDueSoon) text-orange-700
                                                @else text-gray-900 @endif">
                                                {{ $dueDate->format('M d, Y') }}
                                            </span>
                                            @if($isOverdue)
                                            <div class="flex items-center text-xs text-red-600 mt-1">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Overdue by {{ $dueDate->diffForHumans() }}
                                            </div>
                                            @elseif($isDueSoon)
                                            <div class="text-xs text-orange-600 mt-1">
                                                Due {{ $dueDate->diffForHumans() }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    @if($task->created_at)
                                    <div class="flex items-center justify-between p-3 bg-gray-50/50 rounded-lg border border-gray-200/60">
                                        <span class="text-sm font-medium text-gray-700">Created</span>
                                        <div class="text-right">
                                            <span class="text-sm text-gray-900">{{ $task->created_at->format('M d, Y') }}</span>
                                            <div class="text-xs text-gray-500">{{ $task->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($task->updated_at && $task->updated_at != $task->created_at)
                                    <div class="flex items-center justify-between p-3 bg-gray-50/50 rounded-lg border border-gray-200/60">
                                        <span class="text-sm font-medium text-gray-700">Last Updated</span>
                                        <div class="text-right">
                                            <span class="text-sm text-gray-900">{{ $task->updated_at->format('M d, Y') }}</span>
                                            <div class="text-xs text-gray-500">{{ $task->updated_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        @if($task->estimated_hours || $task->actual_hours)
                        <div class="mt-8 pt-6 border-t border-gray-200/60">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4 flex items-center">
                                <i class="fas fa-chart-bar text-purple-500 mr-2 text-xs"></i>
                                Time Tracking
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($task->estimated_hours)
                                <div class="flex items-center justify-between p-4 bg-purple-50/50 rounded-xl border border-purple-200/60">
                                    <span class="text-sm font-medium text-gray-700">Estimated Hours</span>
                                    <span class="text-lg font-bold text-purple-700">{{ $task->estimated_hours }}h</span>
                                </div>
                                @endif
                                @if($task->actual_hours)
                                <div class="flex items-center justify-between p-4 bg-green-50/50 rounded-xl border border-green-200/60">
                                    <span class="text-sm font-medium text-gray-700">Actual Hours</span>
                                    <span class="text-lg font-bold text-green-700">{{ $task->actual_hours }}h</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Project Information Card -->
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200/60 bg-gradient-to-r from-blue-50 to-blue-100/30">
                        <h3 class="font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-folder-open text-blue-600 mr-3"></i>
                            Project Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-medium text-gray-700 mb-1">Project Name</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $task->project->name }}</p>
                            </div>

                            @if($task->project->description)
                            <div>
                                <p class="text-sm font-medium text-gray-700 mb-2">Description</p>
                                <p class="text-sm text-gray-600 leading-relaxed">{{ Str::limit($task->project->description, 120) }}</p>
                            </div>
                            @endif

                            @if($task->project->status)
                            <div class="flex items-center justify-between p-3 bg-gray-50/50 rounded-lg border border-gray-200/60">
                                <span class="text-sm font-medium text-gray-700">Project Status</span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    @if($task->project->status == 'completed') bg-green-100 text-green-800
                                    @elseif($task->project->status == 'in_progress') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ str_replace('_', ' ', ucfirst($task->project->status)) }}
                                </span>
                            </div>
                            @endif

                            <a href="{{ route('team.projects') }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all duration-200 hover:shadow-lg">
                                <i class="fas fa-external-link-alt mr-2 text-xs"></i>
                                View Project
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200/60 bg-gradient-to-r from-green-50 to-green-100/30">
                        <h3 class="font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-bolt text-green-600 mr-3"></i>
                            Quick Actions
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @if($task->status !== 'completed')
                            <button class="w-full group flex items-center justify-between p-4 bg-green-50 hover:bg-green-100 border border-green-200/60 hover:border-green-300 rounded-xl transition-all duration-200 hover:shadow-md">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 group-hover:bg-green-600 rounded-lg flex items-center justify-center mr-3 transition-colors duration-200">
                                        <i class="fas fa-check text-green-600 group-hover:text-white"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="font-semibold text-gray-900">Mark Complete</p>
                                        <p class="text-xs text-gray-500">Update status to completed</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-600"></i>
                            </button>
                            @endif

                            <button class="w-full group flex items-center justify-between p-4 bg-blue-50 hover:bg-blue-100 border border-blue-200/60 hover:border-blue-300 rounded-xl transition-all duration-200 hover:shadow-md">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 group-hover:bg-blue-600 rounded-lg flex items-center justify-center mr-3 transition-colors duration-200">
                                        <i class="fas fa-edit text-blue-600 group-hover:text-white"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="font-semibold text-gray-900">Update Status</p>
                                        <p class="text-xs text-gray-500">Change task progress</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600"></i>
                            </button>

                            <button class="w-full group flex items-center justify-between p-4 bg-purple-50 hover:bg-purple-100 border border-purple-200/60 hover:border-purple-300 rounded-xl transition-all duration-200 hover:shadow-md">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 group-hover:bg-purple-600 rounded-lg flex items-center justify-center mr-3 transition-colors duration-200">
                                        <i class="fas fa-clock text-purple-600 group-hover:text-white"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="font-semibold text-gray-900">Log Time</p>
                                        <p class="text-xs text-gray-500">Track hours worked</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-600"></i>
                            </button>

                            <button class="w-full group flex items-center justify-between p-4 bg-orange-50 hover:bg-orange-100 border border-orange-200/60 hover:border-orange-300 rounded-xl transition-all duration-200 hover:shadow-md">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-orange-100 group-hover:bg-orange-600 rounded-lg flex items-center justify-center mr-3 transition-colors duration-200">
                                        <i class="fas fa-comment text-orange-600 group-hover:text-white"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="font-semibold text-gray-900">Add Comment</p>
                                        <p class="text-xs text-gray-500">Share updates</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 group-hover:text-orange-600"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Task Metadata Card -->
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200/60 bg-gradient-to-r from-gray-50 to-gray-100/30">
                        <h3 class="font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-cog text-gray-600 mr-3"></i>
                            Task Metadata
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Task ID</span>
                                <span class="font-mono text-gray-900">#{{ $task->id }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Created By</span>
                                <span class="text-gray-900">{{ $task->creator->name ?? 'System' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Last Updated</span>
                                <span class="text-gray-900">{{ $task->updated_at->diffForHumans() }}</span>
                            </div>
                            @if($task->assignee)
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600">Assigned To</span>
                                <span class="text-gray-900">{{ $task->assignee->name }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.whitespace-pre-line {
    white-space: pre-line;
}
</style>
@endsection
