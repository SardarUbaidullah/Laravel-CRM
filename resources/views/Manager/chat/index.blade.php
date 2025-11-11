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

    <div class="min-h-screen bg-gray-50 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Messages</h1>
                    @auth
                        @if (Auth::user()->role=='super_admin')
                            <p class="text-gray-600 mt-2 flex items-center text-sm sm:text-base">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                Communicate with your team and clients
                            </p>
                        @endif
                    @endauth
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('manager.projects.index') }}"
                       class="group bg-white border border-gray-300 text-gray-700 hover:bg-primary-50 hover:border-primary-300 hover:text-primary-700 px-4 sm:px-5 py-2.5 rounded-xl font-medium transition-all duration-200 flex items-center shadow-sm hover:shadow-md text-sm sm:text-base">
                        <i class="fas fa-arrow-left mr-2 group-hover:scale-110 transition-transform"></i>
                        Back to Projects
                    </a>
                </div>
            </div>

            <!-- Chat Dashboard -->
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                <!-- Project Chats Section -->
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-200/60 bg-gradient-to-r from-primary-600 to-blue-700">
                        <h2 class="text-base sm:text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-users mr-2 sm:mr-3 text-sm sm:text-lg"></i>
                            My Project Chats
                            <span class="ml-2 bg-white/20 px-2 py-1 rounded-full text-xs font-medium">
                                {{ $projectRooms->count() }} projects
                            </span>
                        </h2>
                    </div>
                    <div class="p-4 sm:p-6 h-[400px] sm:h-[500px] overflow-y-auto custom-scrollbar">
                        @forelse($projectRooms as $room)
                        <div class="group mb-3 sm:mb-4 last:mb-0">
                            <a href="{{ route('manager.chat.project', $room->project) }}"
                               class="block bg-white border border-gray-200/60 rounded-xl p-3 sm:p-4 hover:border-primary-300 hover:shadow-lg transition-all duration-200 group-hover:scale-[1.02]">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3 sm:space-x-4 flex-1 min-w-0">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white shadow-lg flex-shrink-0">
                                            <i class="fas fa-project-diagram text-sm sm:text-lg"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm sm:text-base font-semibold text-gray-900 group-hover:text-primary-700 transition-colors mb-1 truncate">
                                                {{ $room->project->name }}
                                            </h4>
                                            <p class="text-xs sm:text-sm text-gray-500 mb-2">Project Discussion</p>

                                            @if($room->messages->count() > 0)
                                            <div class="mt-2 sm:mt-3 pt-2 sm:pt-3 border-t border-gray-100">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-5 h-5 sm:w-6 sm:h-6 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 text-xs font-bold flex-shrink-0">
                                                        {{ strtoupper(substr($room->messages->first()->user->name, 0, 1)) }}
                                                    </div>
                                                    <p class="text-xs sm:text-sm text-gray-600 truncate flex-1">
                                                        <span class="font-medium text-gray-900">{{ $room->messages->first()->user->name }}</span>:
                                                        {{ Str::limit($room->messages->first()->message, 25) }}
                                                    </p>
                                                </div>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ $room->messages->first()->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            @else
                                            <div class="mt-2 sm:mt-3 pt-2 sm:pt-3 border-t border-gray-100">
                                                <p class="text-xs sm:text-sm text-gray-400 italic">No messages yet</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Unread Badge -->
                                    @if($room->unreadMessagesCount(auth()->id()) > 0)
                                    <div class="flex flex-col items-end space-y-2 flex-shrink-0 ml-2 sm:ml-4">
                                        <span class="bg-red-500 text-white text-xs font-bold w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center animate-pulse">
                                            {{ $room->unreadMessagesCount(auth()->id()) }}
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </a>
                        </div>
                        @empty
                        <div class="text-center py-6 sm:py-8 h-full flex flex-col justify-center">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                <i class="fas fa-users text-gray-400 text-lg sm:text-2xl"></i>
                            </div>
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">No Project Chats</h3>
                            <p class="text-gray-500 text-xs sm:text-sm mb-3 sm:mb-4">
                                @if(auth()->user()->role === 'admin')
                                You don't have any projects assigned as manager yet.
                                @else
                                No project chats available.
                                @endif
                            </p>
                            <a href="{{ route('manager.projects.index') }}"
                               class="inline-flex items-center px-3 sm:px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs sm:text-sm font-medium transition-colors mx-auto">
                                <i class="fas fa-plus mr-2"></i>
                                Create Project
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Direct Messages Section -->
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-200/60 bg-gradient-to-r from-green-600 to-emerald-700">
                        <h2 class="text-base sm:text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-comment-dots mr-2 sm:mr-3 text-sm sm:text-lg"></i>
                            Direct Messages
                            <span class="ml-2 bg-white/20 px-2 py-1 rounded-full text-xs font-medium">
                                {{ $directRooms->count() }} chats
                            </span>
                        </h2>
                    </div>
                    <div class="p-4 sm:p-6 h-[400px] sm:h-[500px] overflow-y-auto custom-scrollbar">
                        @forelse($directRooms as $room)
                        @php
                            $otherUser = $room->participants->where('user_id', '!=', auth()->id())->first()->user;
                        @endphp
                        <div class="group mb-3 sm:mb-4 last:mb-0">
                            <a href="{{ route('manager.chat.direct', $otherUser) }}"
                               class="block bg-white border border-gray-200/60 rounded-xl p-3 sm:p-4 hover:border-green-300 hover:shadow-lg transition-all duration-200 group-hover:scale-[1.02]">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3 sm:space-x-4 flex-1 min-w-0">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white shadow-lg flex-shrink-0">
                                            <span class="font-semibold text-sm sm:text-lg">{{ strtoupper(substr($otherUser->name, 0, 1)) }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2 mb-1 flex-wrap">
                                                <h4 class="text-sm sm:text-base font-semibold text-gray-900 group-hover:text-green-700 transition-colors truncate">
                                                    {{ $otherUser->name }}
                                                </h4>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 flex-shrink-0">
                                                    {{ ucfirst($otherUser->role) }}
                                                </span>
                                            </div>

                                            @if($room->messages->count() > 0)
                                            <div class="mt-2 sm:mt-3 pt-2 sm:pt-3 border-t border-gray-100">
                                                <p class="text-xs sm:text-sm text-gray-600 truncate">
                                                    {{ Str::limit($room->messages->first()->message, 30) }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ $room->messages->first()->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            @else
                                            <div class="mt-2 sm:mt-3 pt-2 sm:pt-3 border-t border-gray-100">
                                                <p class="text-xs sm:text-sm text-gray-400 italic">No messages yet</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Unread Badge -->
                                    @if($room->unreadMessagesCount(auth()->id()) > 0)
                                    <div class="flex flex-col items-end space-y-2 flex-shrink-0 ml-2 sm:ml-4">
                                        <span class="bg-red-500 text-white text-xs font-bold w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center animate-pulse">
                                            {{ $room->unreadMessagesCount(auth()->id()) }}
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </a>
                        </div>
                        @empty
                        <div class="text-center py-6 sm:py-8 h-full flex flex-col justify-center">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                <i class="fas fa-comment text-gray-400 text-lg sm:text-2xl"></i>
                            </div>
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">No Direct Messages</h3>
                            <p class="text-gray-500 text-xs sm:text-sm">Start a conversation with team members</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Start New Chat Section -->
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-200/60 bg-gradient-to-r from-orange-600 to-amber-700">
                        <h2 class="text-base sm:text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-plus mr-2 sm:mr-3 text-sm sm:text-lg"></i>
                            Start New Chat
                        </h2>
                    </div>
                    <div class="p-4 sm:p-6 h-[400px] sm:h-[500px] overflow-y-auto custom-scrollbar">
                        <p class="text-xs sm:text-sm text-gray-600 mb-3 sm:mb-4 flex items-center">
                            <i class="fas fa-info-circle text-orange-500 mr-2 text-xs sm:text-sm"></i>
                            Start a conversation with team members
                        </p>
                        <div class="space-y-2 sm:space-y-3">
                            @foreach($availableUsers as $user)
                            <a href="{{ route('manager.chat.direct', $user) }}"
                               class="group flex items-center space-x-3 sm:space-x-4 p-3 sm:p-4 rounded-xl border border-gray-200/60 hover:border-orange-300 hover:bg-orange-50/30 transition-all duration-200 hover:shadow-md">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center text-white text-xs sm:text-sm font-semibold shadow-md group-hover:scale-110 transition-transform flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs sm:text-sm font-semibold text-gray-900 group-hover:text-orange-700 transition-colors truncate">
                                        {{ $user->name }}
                                    </p>
                                    <p class="text-xs text-gray-500 capitalize">{{ $user->role }}</p>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 group-hover:text-orange-600 transition-colors text-xs sm:text-sm"></i>
                            </a>
                            @endforeach
                        </div>

                        @if($availableUsers->count() === 0)
                        <div class="text-center py-6 sm:py-8 h-full flex flex-col justify-center">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                <i class="fas fa-user-plus text-gray-400 text-lg sm:text-2xl"></i>
                            </div>
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">No Users Available</h3>
                            <p class="text-gray-500 text-xs sm:text-sm">All team members are already in your chats</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom Scrollbar Styles */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
            transition: background 0.2s ease;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Ensure no vertical scroll on body */
        html, body {
            overflow-x: hidden;
        }

        /* Responsive text truncation */
        .truncate-2-lines {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Smooth transitions for all interactive elements */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        /* Mobile optimizations */
        @media (max-width: 640px) {
            .min-h-screen {
                min-height: auto;
            }
        }

        /* Ensure cards don't overflow on small screens */
        @media (max-width: 480px) {
            .rounded-2xl {
                border-radius: 1rem;
            }

            .p-3 {
                padding: 0.75rem;
            }

            .space-x-3 > :not([hidden]) ~ :not([hidden]) {
                --tw-space-x-reverse: 0;
                margin-right: calc(0.75rem * var(--tw-space-x-reverse));
                margin-left: calc(0.75rem * calc(1 - var(--tw-space-x-reverse)));
            }
        }

        /* Fix for very small screens */
        @media (max-width: 360px) {
            .text-sm {
                font-size: 0.75rem;
                line-height: 1rem;
            }

            .text-xs {
                font-size: 0.7rem;
                line-height: 0.875rem;
            }

            .p-3 {
                padding: 0.5rem;
            }
        }
    </style>
@endsection
