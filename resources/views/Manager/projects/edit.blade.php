@extends("Manager.layouts.app")

@section("content")
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <a href="{{ route('manager.projects.show', $project->id) }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Edit Project</h1>
            </div>
            <p class="text-gray-600">Update project details and settings</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Edit Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('manager.projects.update', $project->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Project Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Project Name *</label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $project->name) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                           placeholder="Enter project name"
                           required>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status"
                            id="status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            required>
                        <option value="pending" {{ old('status', $project->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <!-- Manager (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project Manager</label>
                    <div class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                        {{ auth()->user()->name }} (You)
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Project manager cannot be changed</p>
                </div>

                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date"
                           name="start_date"
                           id="start_date"
                           value="{{ old('start_date', $project->start_date) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                </div>

                <!-- Due Date -->
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                    <input type="date"
                           name="due_date"
                           id="due_date"
                           value="{{ old('due_date', $project->due_date) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description"
                          id="description"
                          rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                          placeholder="Enter project description">{{ old('description', $project->description) }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('manager.projects.show', $project->id) }}"
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium">
                    Cancel
                </a>
                <div class="flex items-center space-x-3">
                    <button type="button"
                            onclick="if(confirm('Are you sure you want to reset the form?')) { this.form.reset(); }"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium">
                        Reset
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium shadow-sm">
                        Update Project
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6 mt-8">
        <h3 class="text-lg font-semibold text-red-800 mb-4">Danger Zone</h3>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-700 mb-1">Delete this project permanently</p>
                <p class="text-xs text-gray-500">Once deleted, all project data including tasks and files will be removed.</p>
            </div>
            <form action="{{ route('manager.projects.destroy', $project->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        onclick="return confirm('Are you sure you want to delete this project? This action cannot be undone.')"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200 font-medium text-sm">
                    Delete Project
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Simple date validation
document.addEventListener('DOMContentLoaded', function() {
    const startDate = document.getElementById('start_date');
    const dueDate = document.getElementById('due_date');

    if (startDate && dueDate) {
        startDate.addEventListener('change', function() {
            if (dueDate.value && this.value > dueDate.value) {
                alert('Start date cannot be after due date');
                this.value = '';
            }
        });

        dueDate.addEventListener('change', function() {
            if (startDate.value && this.value < startDate.value) {
                alert('Due date cannot be before start date');
                this.value = '';
            }
        });
    }
});
</script>
@endsection
