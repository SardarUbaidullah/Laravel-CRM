
@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'Manager.layouts.app',
        'user' => 'team.app',

    };
@endphp

@extends($layout)

@section('content')
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>
    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Messages</h1>
@auth
    @if (Auth::user()->role=='super_admin')
  <p class="text-gray-600 mt-2 flex items-center">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    Communicate with your team and clients
                </p>
    @endif
@endauth

            </div>
            <div class="flex space-x-3">
                <a href="{{ route('manager.projects.index') }}"
                   class="group bg-white border border-gray-300 text-gray-700 hover:bg-primary-50 hover:border-primary-300 hover:text-primary-700 px-5 py-2.5 rounded-xl font-medium transition-all duration-200 flex items-center shadow-sm hover:shadow-md">
                    <i class="fas fa-arrow-left mr-2 group-hover:scale-110 transition-transform"></i>
                    Back to Projects
                </a>
            </div>
        </div>

        <!-- Chat Dashboard -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Project Chats Section -->
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200/60 bg-gradient-to-r from-primary-600 to-blue-700">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-users mr-3 text-lg"></i>
                        My Project Chats
                        <span class="ml-2 bg-white/20 px-2.5 py-1 rounded-full text-sm font-medium">
                            {{ $projectRooms->count() }} projects
                        </span>
                    </h2>
                </div>
                <div class="p-6 max-h-[500px] overflow-y-auto">
                    @forelse($projectRooms as $room)
                    <div class="group mb-4 last:mb-0">
                        <a href="{{ route('manager.chat.project', $room->project) }}"
                           class="block bg-white border border-gray-200/60 rounded-xl p-5 hover:border-primary-300 hover:shadow-lg transition-all duration-200 group-hover:scale-[1.02]">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4 flex-1">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white shadow-lg flex-shrink-0">
                                        <i class="fas fa-project-diagram text-lg"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-base font-semibold text-gray-900 group-hover:text-primary-700 transition-colors mb-1">
                                            {{ $room->project->name }}
                                        </h4>
                                        <p class="text-sm text-gray-500 mb-2">Project Discussion</p>

                                        @if($room->messages->count() > 0)
                                        <div class="mt-3 pt-3 border-t border-gray-100">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 text-xs font-bold">
                                                    {{ strtoupper(substr($room->messages->first()->user->name, 0, 1)) }}
                                                </div>
                                                <p class="text-sm text-gray-600 truncate flex-1">
                                                    <span class="font-medium text-gray-900">{{ $room->messages->first()->user->name }}</span>:
                                                    {{ Str::limit($room->messages->first()->message, 35) }}
                                                </p>
                                            </div>
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ $room->messages->first()->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        @else
                                        <div class="mt-3 pt-3 border-t border-gray-100">
                                            <p class="text-sm text-gray-400 italic">No messages yet</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Unread Badge -->
                                @if($room->unreadMessagesCount(auth()->id()) > 0)
                                <div class="flex flex-col items-end space-y-2 flex-shrink-0 ml-4">
                                    <span class="bg-red-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center animate-pulse">
                                        {{ $room->unreadMessagesCount(auth()->id()) }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Project Chats</h3>
                        <p class="text-gray-500 text-sm mb-4">
                            @if(auth()->user()->role === 'admin')
                            You don't have any projects assigned as manager yet.
                            @else
                            No project chats available.
                            @endif
                        </p>
                        <a href="{{ route('manager.projects.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Create Project
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Direct Messages Section -->
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200/60 bg-gradient-to-r from-green-600 to-emerald-700">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-comment-dots mr-3 text-lg"></i>
                        Direct Messages
                        <span class="ml-2 bg-white/20 px-2.5 py-1 rounded-full text-sm font-medium">
                            {{ $directRooms->count() }} chats
                        </span>
                    </h2>
                </div>
                <div class="p-6 max-h-[500px] overflow-y-auto">
                    @forelse($directRooms as $room)
                    @php
                        $otherUser = $room->participants->where('user_id', '!=', auth()->id())->first()->user;
                    @endphp
                    <div class="group mb-4 last:mb-0">
                        <a href="{{ route('manager.chat.direct', $otherUser) }}"
                           class="block bg-white border border-gray-200/60 rounded-xl p-5 hover:border-green-300 hover:shadow-lg transition-all duration-200 group-hover:scale-[1.02]">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4 flex-1">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white shadow-lg flex-shrink-0">
                                        <span class="font-semibold text-lg">{{ strtoupper(substr($otherUser->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <h4 class="text-base font-semibold text-gray-900 group-hover:text-green-700 transition-colors">
                                                {{ $otherUser->name }}
                                            </h4>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ ucfirst($otherUser->role) }}
                                            </span>
                                        </div>

                                        @if($room->messages->count() > 0)
                                        <div class="mt-3 pt-3 border-t border-gray-100">
                                            <p class="text-sm text-gray-600 truncate">
                                                {{ Str::limit($room->messages->first()->message, 40) }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ $room->messages->first()->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        @else
                                        <div class="mt-3 pt-3 border-t border-gray-100">
                                            <p class="text-sm text-gray-400 italic">No messages yet</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Unread Badge -->
                                @if($room->unreadMessagesCount(auth()->id()) > 0)
                                <div class="flex flex-col items-end space-y-2 flex-shrink-0 ml-4">
                                    <span class="bg-red-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center animate-pulse">
                                        {{ $room->unreadMessagesCount(auth()->id()) }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comment text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Direct Messages</h3>
                        <p class="text-gray-500 text-sm">Start a conversation with team members</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Start New Chat Section -->
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200/60 bg-gradient-to-r from-orange-600 to-amber-700">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-plus mr-3 text-lg"></i>
                        Start New Chat
                    </h2>
                </div>
                <div class="p-6 max-h-[500px] overflow-y-auto">
                    <p class="text-sm text-gray-600 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                        Start a conversation with team members
                    </p>
                    <div class="space-y-3">
                        @foreach($availableUsers as $user)
                        <a href="{{ route('manager.chat.direct', $user) }}"
                           class="group flex items-center space-x-4 p-4 rounded-xl border border-gray-200/60 hover:border-orange-300 hover:bg-orange-50/30 transition-all duration-200 hover:shadow-md">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center text-white text-sm font-semibold shadow-md group-hover:scale-110 transition-transform">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 group-hover:text-orange-700 transition-colors">
                                    {{ $user->name }}
                                </p>
                                <p class="text-xs text-gray-500 capitalize">{{ $user->role }}</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-orange-600 transition-colors"></i>
                        </a>
                        @endforeach
                    </div>

                    @if($availableUsers->count() === 0)
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-plus text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Users Available</h3>
                        <p class="text-gray-500 text-sm">All team members are already in your chats</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
