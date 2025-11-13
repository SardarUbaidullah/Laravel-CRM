@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
            <div class="mb-4 lg:mb-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Users Management</h1>
                <p class="text-gray-600 mt-2 text-sm sm:text-base">Manage all system users and their permissions</p>
            </div>
            <a href="{{ route('users.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-3 rounded-xl font-medium transition duration-200 flex items-center justify-center shadow-sm w-full lg:w-auto">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add New User
            </a>
        </div>

        <!-- Professional Mobile Filter Tabs -->
        <div class="lg:hidden mb-6">
            <div class="flex space-x-1 bg-white p-1 rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
                <button data-role="super_admin" class="filter-tab active flex-1 px-4 py-3 text-sm font-medium rounded-lg bg-purple-100 text-purple-800 whitespace-nowrap transition-all duration-200">
                    Super Admins ({{ $users->where('role', 'super_admin')->count() }})
                </button>
                <button data-role="admin" class="filter-tab flex-1 px-4 py-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                    Managers ({{ $users->where('role', 'admin')->count() }})
                </button>
                <button data-role="user" class="filter-tab flex-1 px-4 py-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                    Team ({{ $users->where('role', 'user')->count() }})
                </button>
                <button data-role="client" class="filter-tab flex-1 px-4 py-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                    Clients ({{ $users->where('role', 'client')->count() }})
                </button>
            </div>
        </div>

        <!-- Kanban Board -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <!-- Super Admins Column -->
            <div class="user-column active bg-gray-50 rounded-2xl p-4 sm:p-6" data-role="super_admin">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-2 h-2 sm:w-3 sm:h-3 bg-purple-500 rounded-full mr-2 sm:mr-3"></div>
                        Super Admins
                    </h3>
                    <span class="bg-purple-100 text-purple-800 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">
                        {{ $users->where('role', 'super_admin')->count() }}
                    </span>
                </div>
                <div class="space-y-3 sm:space-y-4">
                    @foreach($users->where('role', 'super_admin') as $user)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <div class="flex items-center space-x-2 sm:space-x-3">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-purple-500 flex items-center justify-center text-white font-semibold text-sm sm:text-lg shadow-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $user->name }}</h4>
                                    <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs sm:text-sm mb-3 sm:mb-4">
                            <span class="bg-purple-100 text-purple-800 px-2 sm:px-3 py-1 rounded-lg text-xs font-medium">
                                Super Admin
                            </span>
                            <span class="text-gray-400 text-xs">
                                #{{ $user->id }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-3 sm:pt-4 border-t border-gray-100">
                            <div class="text-xs text-gray-500 truncate max-w-[120px] sm:max-w-none">
                                @if($user->client)
                                <span class="flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="truncate">{{ $user->client->name }}</span>
                                </span>
                                @endif
                            </div>
                            <div class="flex space-x-1 sm:space-x-2">
                                <a href="{{ route('users.show', $user) }}"
                                   class="text-green-600 hover:text-green-800 p-1 sm:p-2 rounded-lg hover:bg-green-50 transition-colors"
                                   title="View User">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-blue-600 hover:text-blue-800 p-1 sm:p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                   title="Edit User">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this user?')"
                                            class="text-red-600 hover:text-red-800 p-1 sm:p-2 rounded-lg hover:bg-red-50 transition-colors"
                                            title="Delete User">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Managers Column -->
            <div class="user-column bg-gray-50 rounded-2xl p-4 sm:p-6" data-role="admin">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-2 h-2 sm:w-3 sm:h-3 bg-blue-500 rounded-full mr-2 sm:mr-3"></div>
                        Managers
                    </h3>
                    <span class="bg-blue-100 text-blue-800 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">
                        {{ $users->where('role', 'admin')->count() }}
                    </span>
                </div>
                <div class="space-y-3 sm:space-y-4">
                    @foreach($users->where('role', 'admin') as $user)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <div class="flex items-center space-x-2 sm:space-x-3">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-500 flex items-center justify-center text-white font-semibold text-sm sm:text-lg shadow-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $user->name }}</h4>
                                    <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs sm:text-sm mb-3 sm:mb-4">
                            <span class="bg-blue-100 text-blue-800 px-2 sm:px-3 py-1 rounded-lg text-xs font-medium">
                                Manager
                            </span>
                            <span class="text-gray-400 text-xs">
                                #{{ $user->id }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-3 sm:pt-4 border-t border-gray-100">
                            <div class="text-xs text-gray-500 truncate max-w-[120px] sm:max-w-none">
                                @if($user->client)
                                <span class="flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="truncate">{{ $user->client->name }}</span>
                                </span>
                                @endif
                            </div>
                            <div class="flex space-x-1 sm:space-x-2">
                                <a href="{{ route('users.show', $user) }}"
                                   class="text-green-600 hover:text-green-800 p-1 sm:p-2 rounded-lg hover:bg-green-50 transition-colors"
                                   title="View User">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-blue-600 hover:text-blue-800 p-1 sm:p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                   title="Edit User">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this user?')"
                                            class="text-red-600 hover:text-red-800 p-1 sm:p-2 rounded-lg hover:bg-red-50 transition-colors"
                                            title="Delete User">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Team Members Column -->
            <div class="user-column bg-gray-50 rounded-2xl p-4 sm:p-6" data-role="user">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-2 h-2 sm:w-3 sm:h-3 bg-green-500 rounded-full mr-2 sm:mr-3"></div>
                        Team Members
                    </h3>
                    <span class="bg-green-100 text-green-800 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">
                        {{ $users->where('role', 'user')->count() }}
                    </span>
                </div>
                <div class="space-y-3 sm:space-y-4">
                    @foreach($users->where('role', 'user') as $user)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <div class="flex items-center space-x-2 sm:space-x-3">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-green-500 flex items-center justify-center text-white font-semibold text-sm sm:text-lg shadow-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $user->name }}</h4>
                                    <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs sm:text-sm mb-3 sm:mb-4">
                            <span class="bg-green-100 text-green-800 px-2 sm:px-3 py-1 rounded-lg text-xs font-medium">
                                Team Member
                            </span>
                            <span class="text-gray-400 text-xs">
                                #{{ $user->id }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-3 sm:pt-4 border-t border-gray-100">
                            <div class="text-xs text-gray-500 truncate max-w-[120px] sm:max-w-none">
                                @if($user->client)
                                <span class="flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="truncate">{{ $user->client->name }}</span>
                                </span>
                                @endif
                            </div>
                            <div class="flex space-x-1 sm:space-x-2">
                                <a href="{{ route('users.show', $user) }}"
                                   class="text-green-600 hover:text-green-800 p-1 sm:p-2 rounded-lg hover:bg-green-50 transition-colors"
                                   title="View User">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-blue-600 hover:text-blue-800 p-1 sm:p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                   title="Edit User">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this user?')"
                                            class="text-red-600 hover:text-red-800 p-1 sm:p-2 rounded-lg hover:bg-red-50 transition-colors"
                                            title="Delete User">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Clients Column -->
            <div class="user-column bg-gray-50 rounded-2xl p-4 sm:p-6" data-role="client">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-2 h-2 sm:w-3 sm:h-3 bg-orange-500 rounded-full mr-2 sm:mr-3"></div>
                        Clients
                    </h3>
                    <span class="bg-orange-100 text-orange-800 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">
                        {{ $users->where('role', 'client')->count() }}
                    </span>
                </div>
                <div class="space-y-3 sm:space-y-4">
                    @foreach($users->where('role', 'client') as $user)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <div class="flex items-center space-x-2 sm:space-x-3">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-orange-500 flex items-center justify-center text-white font-semibold text-sm sm:text-lg shadow-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $user->name }}</h4>
                                    <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs sm:text-sm mb-3 sm:mb-4">
                            <span class="bg-orange-100 text-orange-800 px-2 sm:px-3 py-1 rounded-lg text-xs font-medium">
                                Client
                            </span>
                            <span class="text-gray-400 text-xs">
                                #{{ $user->id }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-3 sm:pt-4 border-t border-gray-100">
                            <div class="text-xs text-gray-500 truncate max-w-[120px] sm:max-w-none">
                                @if($user->client)
                                <span class="flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="truncate">{{ $user->client->name }}</span>
                                </span>
                                @endif
                            </div>
                            <div class="flex space-x-1 sm:space-x-2">
                                <a href="{{ route('users.show', $user) }}"
                                   class="text-green-600 hover:text-green-800 p-1 sm:p-2 rounded-lg hover:bg-green-50 transition-colors"
                                   title="View User">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-blue-600 hover:text-blue-800 p-1 sm:p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                   title="Edit User">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this user?')"
                                            class="text-red-600 hover:text-red-800 p-1 sm:p-2 rounded-lg hover:bg-red-50 transition-colors"
                                            title="Delete User">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Empty State -->
        @if($users->count() == 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 text-center py-12 sm:py-16 px-4">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-sm">
                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2 sm:mb-3">No users found</h3>
            <p class="text-gray-600 mb-6 sm:mb-8 max-w-md mx-auto text-sm sm:text-base">Get started by adding your first team member or client to the system</p>
            <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 sm:px-8 py-3 rounded-xl font-medium transition duration-200 inline-flex items-center justify-center shadow-sm w-full sm:w-auto">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add First User
            </a>
        </div>
        @endif
    </div>
</div>

<script>
// Professional filter - clean and working
document.addEventListener('DOMContentLoaded', function() {
    const filterTabs = document.querySelectorAll('.filter-tab');
    const userColumns = document.querySelectorAll('.user-column');

    // Initialize mobile view
    function initMobileView() {
        if (window.innerWidth < 1024) {
            userColumns.forEach((col, index) => {
                if (index === 0) {
                    col.style.display = 'block';
                } else {
                    col.style.display = 'none';
                }
            });
        } else {
            userColumns.forEach(col => {
                col.style.display = 'block';
            });
        }
    }

    // Filter tab click handler
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const role = this.getAttribute('data-role');

            // Update active tab
            filterTabs.forEach(t => {
                t.classList.remove('active', 'bg-purple-100', 'text-purple-800');
                t.classList.add('text-gray-600', 'hover:bg-gray-100');
            });
            this.classList.remove('text-gray-600', 'hover:bg-gray-100');
            this.classList.add('active', 'bg-purple-100', 'text-purple-800');

            // Show selected column, hide others on mobile
            if (window.innerWidth < 1024) {
                userColumns.forEach(col => {
                    if (col.getAttribute('data-role') === role) {
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
    .user-column {
        display: none;
    }
    .user-column:first-child {
        display: block;
    }
}

.filter-tab.active {
    background-color: rgb(243, 232, 255);
    color: rgb(107, 33, 168);
}

.filter-tab {
    transition: all 0.2s ease-in-out;
}
</style>
@endsection
