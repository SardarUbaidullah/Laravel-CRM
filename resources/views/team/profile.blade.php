@extends('team.app')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
        <p class="text-gray-600">Manage your account information</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Personal Information</h2>
                </div>
                <div class="p-6">
                    <form>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input type="text" value="{{ $user->name }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" value="{{ $user->email }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                                <input type="text" value="{{ ucfirst($user->role) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                       readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Member Since</label>
                                <input type="text" value="{{ $user->created_at->format('M d, Y') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                       readonly>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Profile Summary -->
        <div class="space-y-6">
            <!-- User Card -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 text-center">
                <img class="w-20 h-20 rounded-full mx-auto mb-4 border-2 border-gray-200"
                     src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=120&background=3B82F6&color=fff"
                     alt="{{ $user->name }}">
                <h3 class="font-semibold text-gray-900">{{ $user->name }}</h3>
                <p class="text-sm text-gray-500">{{ ucfirst($user->role) }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $user->email }}</p>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">My Stats</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Tasks</span>
                        <span class="font-medium text-gray-900">
                            {{ \App\Models\Tasks::where('assigned_to', $user->id)->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Completed</span>
                        <span class="font-medium text-green-600">
                            {{ \App\Models\Tasks::where('assigned_to', $user->id)->where('status', 'done')->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Pending</span>
                        <span class="font-medium text-orange-600">
                            {{ \App\Models\Tasks::where('assigned_to', $user->id)->whereIn('status', ['todo', 'in_progress'])->count() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
