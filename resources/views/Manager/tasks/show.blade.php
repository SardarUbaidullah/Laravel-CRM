@extends("Manager.layouts.app")

@section("content")
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <a href="{{ route('manager.tasks.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">{{ $task->title }}</h1>
            </div>
            <p class="text-gray-600">Task details and information</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('manager.tasks.edit', $task->id) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Task
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Task Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Task Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Task Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Basic Details</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Task Title</dt>
                                <dd class="text-sm text-gray-900">{{ $task->title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Project</dt>
                                <dd class="text-sm text-gray-900">{{ $task->project->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Created By</dt>
                                <dd class="text-sm text-gray-900">{{ $task->user->name ?? 'System' }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Status & Priority</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Status</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($task->status == 'done') bg-green-100 text-green-800
                                        @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Priority</dt>
                                <dd>
                                    @if($task->priority)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($task->priority == 'high') bg-red-100 text-red-800
                                            @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">Not set</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Assigned To</dt>
                                <dd class="text-sm text-gray-900">{{ $task->assignee->name ?? 'Unassigned' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                @if($task->description)
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                    <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-4">{{ $task->description }}</p>
                </div>
                @endif
            </div>

            <!-- Subtasks (if any) -->
            @if($task->subtasks && $task->subtasks->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Subtasks</h2>
                <div class="space-y-3">
                    @foreach($task->subtasks as $subtask)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-6 h-6 border-2 border-gray-300 rounded flex items-center justify-center">
                                @if($subtask->status == 'done')
                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $subtask->title }}</p>
                                <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $subtask->status) }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Timeline & Actions -->
        <div class="space-y-6">
            <!-- Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                <div class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-600">Created</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $task->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-600">Last Updated</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $task->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                    @if($task->due_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-600">Due Date</dt>
                        <dd class="text-sm text-gray-900 mt-1">
                            @if(\Carbon\Carbon::parse($task->due_date)->isPast() && $task->status != 'done')
                                <span class="text-red-600 font-medium">
                                    {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }} (Overdue)
                                </span>
                            @else
                                {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                            @endif
                        </dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('manager.tasks.edit', $task->id) }}"
                       class="w-full flex items-center space-x-3 p-3 text-left text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition duration-150">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Edit Task</span>
                    </a>
                    <a href="{{ route('manager.projects.show', $task->project_id) }}"
                       class="w-full flex items-center space-x-3 p-3 text-left text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition duration-150">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span>View Project</span>
                    </a>
                    @if($task->assignee)
                    <a href="{{ route('manager.team.show', $task->assigned_to) }}"
                       class="w-full flex items-center space-x-3 p-3 text-left text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition duration-150">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        <span>View Assignee</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6">
                <h3 class="text-lg font-semibold text-red-800 mb-4">Danger Zone</h3>
                <form action="{{ route('manager.tasks.destroy', $task->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Are you sure you want to delete this task? This action cannot be undone.')"
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200 font-medium text-sm">
                        Delete Task
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
