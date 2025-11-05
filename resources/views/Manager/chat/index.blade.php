<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Dashboard - CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
</head>
<body class="bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Messages</h1>
                <p class="text-gray-600">Communicate with your team and clients</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('manager.projects.index') }}"
                   class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Projects
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar - Chat Rooms -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Project Chats -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-primary-500 to-primary-600 px-4 py-3">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-users mr-2"></i>
                            Project Chats
                        </h3>
                    </div>
                    <div class="p-4 max-h-96 overflow-y-auto">
                        @forelse($projectRooms as $room)
                        <a href="{{ route('manager.chat.project', $room->project) }}"
                           class="flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-50 transition duration-150 mb-2 border border-gray-100">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $room->name }}</h4>
                                <p class="text-xs text-gray-500 truncate">{{ $room->project->name }}</p>
                                @if($room->messages->count() > 0)
                                <p class="text-xs text-gray-600 mt-1 truncate">
                                    {{ $room->messages->first()->user->name }}:
                                    {{ Str::limit($room->messages->first()->message, 30) }}
                                </p>
                                @endif
                            </div>
                            @if($room->unreadMessagesCount(auth()->id()) > 0)
                            <span class="bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                                {{ $room->unreadMessagesCount(auth()->id()) }}
                            </span>
                            @endif
                        </a>
                        @empty
                        <div class="text-center py-6">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-users text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 text-sm">No project chats yet</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Direct Messages -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-4 py-3">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-comment-dots mr-2"></i>
                            Direct Messages
                        </h3>
                    </div>
                    <div class="p-4 max-h-96 overflow-y-auto">
                        @forelse($directRooms as $room)
                        @php
                            $otherUser = $room->participants->where('user_id', '!=', auth()->id())->first()->user;
                        @endphp
                        <a href="{{ route('manager.chat.direct', $otherUser) }}"
                           class="flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-50 transition duration-150 mb-2 border border-gray-100">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900">{{ $otherUser->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $otherUser->role }}</p>
                                @if($room->messages->count() > 0)
                                <p class="text-xs text-gray-600 mt-1 truncate">
                                    {{ Str::limit($room->messages->first()->message, 30) }}
                                </p>
                                @endif
                            </div>
                            @if($room->unreadMessagesCount(auth()->id()) > 0)
                            <span class="bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                                {{ $room->unreadMessagesCount(auth()->id()) }}
                            </span>
                            @endif
                        </a>
                        @empty
                        <div class="text-center py-6">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-comment text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 text-sm">No direct messages</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Start New Chat -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-4 py-3">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Start New Chat
                        </h3>
                    </div>
                    <div class="p-4">
                        <p class="text-sm text-gray-600 mb-3">Start a conversation with:</p>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @foreach($availableUsers as $user)
                            <a href="{{ route('manager.chat.direct', $user) }}"
                               class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition duration-150">
                                <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ $user->role }}</p>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content - Welcome/Instructions -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                    <div class="w-24 h-24 bg-gradient-to-r from-primary-500 to-primary-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-comments text-white text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Welcome to Messages</h2>
                    <p class="text-gray-600 max-w-md mx-auto mb-6">
                        Select a project chat or start a direct conversation to begin messaging with your team members and collaborators.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-2xl mx-auto">
                        <div class="bg-primary-50 rounded-xl p-4">
                            <i class="fas fa-users text-primary-600 text-xl mb-2"></i>
                            <h4 class="font-semibold text-gray-900">Project Chats</h4>
                            <p class="text-sm text-gray-600 mt-1">Team discussions for each project</p>
                        </div>
                        <div class="bg-green-50 rounded-xl p-4">
                            <i class="fas fa-comment text-green-600 text-xl mb-2"></i>
                            <h4 class="font-semibold text-gray-900">Direct Messages</h4>
                            <p class="text-sm text-gray-600 mt-1">Private one-on-one conversations</p>
                        </div>
                        <div class="bg-orange-50 rounded-xl p-4">
                            <i class="fas fa-paperclip text-orange-600 text-xl mb-2"></i>
                            <h4 class="font-semibold text-gray-900">File Sharing</h4>
                            <p class="text-sm text-gray-600 mt-1">Share files and documents</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
