<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->name }} Chat - CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
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
                        },
                        chat: {
                            sent: '#dcf8c6',
                            received: '#ffffff'
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                        'slide-in': 'slideIn 0.3s ease-out'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideIn: {
                            '0%': { opacity: '0', transform: 'translateX(-10px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .scrollbar-thin {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Typing indicator animation */
        @keyframes typing {
            0% { opacity: 0.3; }
            50% { opacity: 1; }
            100% { opacity: 0.3; }
        }
        .typing-dot {
            animation: typing 1.4s infinite ease-in-out;
        }
        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                <a href="{{ route('manager.chat.index') }}"
                   class="group bg-white hover:bg-primary-50 border border-gray-200 rounded-xl p-3 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-arrow-left text-gray-600 group-hover:text-primary-600 text-lg transition-colors duration-200"></i>
                </a>
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-comments text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h1>
                        <p class="text-gray-600 flex items-center">
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium mr-2">
                                <i class="fas fa-circle text-xs mr-1"></i>
                                Active Chat
                            </span>
                            Project Discussion
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('manager.projects.show', $project) }}"
                   class="group bg-white hover:bg-primary-600 border border-primary-200 text-primary-700 hover:text-white px-5 py-2.5 rounded-xl font-medium transition-all duration-200 flex items-center shadow-sm hover:shadow-lg">
                    <i class="fas fa-external-link-alt mr-2 group-hover:scale-110 transition-transform duration-200"></i>
                    View Project
                </a>
                <div class="flex items-center space-x-3 bg-white rounded-xl px-4 py-2 shadow-sm border border-gray-200">
                    <div class="flex -space-x-3">
                        @foreach($chatRoom->participants->take(4) as $participant)
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold border-2 border-white shadow-lg transition-transform duration-200 hover:scale-110 cursor-pointer"
                             title="{{ $participant->user->name }} ({{ $participant->user->role }})">
                            {{ strtoupper(substr($participant->user->name, 0, 1)) }}
                        </div>
                        @endforeach
                    </div>
                    @if($chatRoom->participants->count() > 4)
                    <div class="text-sm text-gray-500 font-medium bg-gray-100 px-3 py-1 rounded-full">
                        +{{ $chatRoom->participants->count() - 4 }} more
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            <!-- Chat Sidebar -->
            <div class="xl:col-span-1 space-y-6">
                <!-- Project Info Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 transition-all duration-200 hover:shadow-xl">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center text-white">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg">Project Overview</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-flag text-blue-500"></i>
                                <span class="text-sm font-medium text-gray-700">Status</span>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                @if($project->status == 'completed') bg-green-100 text-green-800
                                @elseif($project->status == 'in_progress') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                <i class="fas fa-circle text-xs mr-1.5"></i>
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-calendar-day text-purple-500"></i>
                                <span class="text-sm font-medium text-gray-700">Due Date</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ $project->due_date ? \Carbon\Carbon::parse($project->due_date)->format('M d, Y') : 'Not set' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Team Members Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 transition-all duration-200 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center text-white">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="font-bold text-gray-900 text-lg">Team Members</h3>
                        </div>
                        <span class="bg-primary-100 text-primary-800 px-3 py-1 rounded-full text-sm font-semibold">
                            {{ $chatRoom->participants->count() }}
                        </span>
                    </div>
                    <div class="space-y-3 max-h-80 overflow-y-auto scrollbar-thin">
                        @foreach($chatRoom->participants as $participant)
                        <div class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-200 hover:bg-primary-50 group border border-transparent hover:border-primary-200">
                            <div class="relative">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold shadow-md group-hover:scale-105 transition-transform duration-200">
                                    {{ strtoupper(substr($participant->user->name, 0, 1)) }}
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $participant->user->name }}</p>
                                    @if($participant->user->id === auth()->id())
                                    <span class="bg-primary-600 text-white px-2 py-0.5 rounded-full text-xs font-medium">You</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 capitalize bg-gray-100 px-2 py-1 rounded-full inline-block mt-1">
                                    {{ $participant->user->role }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Chat Main Area -->
            <div class="xl:col-span-3">
                <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col h-[700px] transform transition-all duration-200 hover:shadow-2xl">
                    <!-- Chat Header -->
                    <div class="bg-gradient-to-r from-primary-600 to-blue-700 px-6 py-5 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center text-white shadow-lg">
                                    <i class="fas fa-comments text-lg"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-white">Project Discussion</h2>
                                    <p class="text-blue-100 text-sm flex items-center">
                                        <i class="fas fa-users mr-1.5"></i>
                                        <span id="online-count">{{ $chatRoom->participants->count() }}</span> participants in chat
                                        <div class="flex items-center space-x-2 ml-4">
                                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                            <span class="text-blue-100 text-xs font-medium" id="connection-text">Live Connected</span>
                                        </div>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button class="w-10 h-10 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-xl flex items-center justify-center text-white transition-all duration-200 hover:scale-110 shadow-md">
                                    <i class="fas fa-phone-alt text-sm"></i>
                                </button>
                                <button class="w-10 h-10 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-xl flex items-center justify-center text-white transition-all duration-200 hover:scale-110 shadow-md">
                                    <i class="fas fa-video text-sm"></i>
                                </button>
                                <button class="w-10 h-10 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-xl flex items-center justify-center text-white transition-all duration-200 hover:scale-110 shadow-md">
                                    <i class="fas fa-ellipsis-v text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div class="flex-1 p-6 overflow-y-auto bg-gradient-to-b from-gray-50 to-blue-50 scrollbar-thin" id="messages-container">
                        <div class="space-y-4" id="messages-list">
                            @foreach($messages as $message)
                            <div class="flex items-start space-x-3 animate-fade-in {{ $message->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                                <div class="w-10 h-10 {{ $message->user_id === auth()->id() ? 'bg-gradient-to-br from-green-500 to-emerald-600' : 'bg-gradient-to-br from-primary-500 to-blue-600' }} rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0 shadow-lg transition-transform duration-200 hover:scale-105 cursor-pointer"
                                     title="{{ $message->user->name }} ({{ $message->user->role }})">
                                    {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 max-w-md {{ $message->user_id === auth()->id() ? 'text-right' : '' }}">
                                    <div class="inline-block {{ $message->user_id === auth()->id() ? 'bg-chat-sent' : 'bg-chat-received' }} rounded-2xl px-4 py-3 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-200">
                                        <div class="flex items-center space-x-2 mb-2 {{ $message->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                                            <span class="text-sm font-bold {{ $message->user_id === auth()->id() ? 'text-green-800' : 'text-primary-700' }}">{{ $message->user->name }}</span>
                                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">{{ $message->formatted_time }}</span>
                                        </div>
                                        <p class="text-gray-800 text-sm leading-relaxed">{{ $message->message }}</p>
                                        @if($message->attachment)
                                        <div class="mt-3">
                                            <a href="{{ Storage::url($message->attachment) }}"
                                               target="_blank"
                                               class="inline-flex items-center space-x-2 text-xs text-primary-700 hover:text-primary-800 bg-primary-100 hover:bg-primary-200 rounded-lg px-3 py-2 transition-all duration-200 hover:shadow-md border border-primary-200">
                                                <i class="fas fa-paperclip"></i>
                                                <span class="font-medium">{{ $message->attachment_name }}</span>
                                                <i class="fas fa-external-link-alt text-xs"></i>
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Typing Indicator -->
                        <div id="typing-indicator" class="hidden flex items-start space-x-3 mt-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0 shadow-lg">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="bg-chat-received rounded-2xl px-4 py-3 shadow-lg border border-gray-200">
                                <div class="flex space-x-1">
                                    <div class="typing-dot w-2 h-2 bg-gray-500 rounded-full"></div>
                                    <div class="typing-dot w-2 h-2 bg-gray-500 rounded-full"></div>
                                    <div class="typing-dot w-2 h-2 bg-gray-500 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="border-t border-gray-200 bg-white p-6 shadow-lg">
                        <div class="flex space-x-4 items-end" id="message-form">
                            <div class="flex-1 relative">
                                <textarea
                                    id="message-input"
                                    rows="1"
                                    placeholder="Type your message... (Press Enter to send, Shift+Enter for new line)"
                                    class="w-full px-5 py-4 border-2 border-gray-300 rounded-2xl resize-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 shadow-sm focus:shadow-md bg-gray-50 focus:bg-white"
                                ></textarea>
                                <div class="absolute right-3 bottom-3 flex space-x-2">
                                    <span class="text-xs text-gray-400 bg-white px-2 py-1 rounded-full border">
                                        Enter ⏎
                                    </span>
                                </div>
                            </div>
                            <div class="flex space-x-3">
                                <label for="attachment" class="cursor-pointer group">
                                    <input type="file" id="attachment" class="hidden">
                                    <span class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 group-hover:from-purple-600 group-hover:to-pink-700 rounded-xl flex items-center justify-center text-white transition-all duration-200 shadow-lg group-hover:shadow-xl group-hover:scale-105">
                                        <i class="fas fa-paperclip text-lg"></i>
                                    </span>
                                </label>
                                <button
                                    id="send-button"
                                    type="button"
                                    class="w-14 h-14 bg-gradient-to-br from-primary-600 to-blue-700 hover:from-primary-700 hover:to-blue-800 rounded-xl flex items-center justify-center text-white transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95"
                                >
                                    <i class="fas fa-paper-plane text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Connection Status Indicator -->
    <div id="connection-status" class="fixed bottom-4 left-4 bg-green-500 text-white px-3 py-2 rounded-lg shadow-lg z-40">
        <div class="flex items-center space-x-2">
            <i class="fas fa-wifi"></i>
            <span class="text-sm font-medium">Live Connected</span>
        </div>
    </div>

  <script>
    // REAL-TIME CHAT - FIXED PUSHER CONNECTION
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Initializing real-time chat...');

        const currentUserId = {{ auth()->id() }};

        let pusher = null;

        // Auto-scroll to bottom
        const messagesContainer = document.getElementById('messages-container');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        // Auto-resize textarea
        const textarea = document.getElementById('message-input');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Send message function
        async function sendMessage() {
            const messageInput = document.getElementById('message-input');
            const attachmentInput = document.getElementById('attachment');
            const sendButton = document.getElementById('send-button');

            const messageText = messageInput.value.trim();

            if (!messageText) {
                showNotification('Please enter a message', 'error');
                return;
            }

            // Show sending state
            const originalHtml = sendButton.innerHTML;
            sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            sendButton.disabled = true;

            const formData = new FormData();
            formData.append('message', messageText);
            formData.append('_token', '{{ csrf_token() }}');

            if (attachmentInput.files[0]) {
                formData.append('attachment', attachmentInput.files[0]);
            }

            try {
                console.log('📤 Sending message...');

                const response = await fetch('{{ route('manager.chat.send', $chatRoom) }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();
                console.log('📥 Server response:', data);

                if (data.success) {
                    // IMMEDIATELY add message to UI
                    console.log('✅ Adding message to UI immediately:', data.message_data);
                    addMessageToChat(data.message_data);

                    // Clear input fields
                    messageInput.value = '';
                    attachmentInput.value = '';
                    messageInput.style.height = 'auto';

                    // Show success state
                    sendButton.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(() => {
                        sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
                        sendButton.disabled = false;
                    }, 1000);

                } else {
                    throw new Error(data.error || 'Failed to send message');
                }
            } catch (error) {
                console.error('❌ Error sending message:', error);
                sendButton.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                setTimeout(() => {
                    sendButton.innerHTML = originalHtml;
                    sendButton.disabled = false;
                }, 2000);
                showNotification('Failed to send message: ' + error.message, 'error');
            }
        }

        // Event listeners
        const messageForm = document.getElementById('message-form');
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            sendMessage();
            return false;
        });

        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Initialize Pusher - FIXED CONNECTION
        function initializePusher() {
            try {
                console.log('🔧 Initializing Pusher...');

                // Get credentials from environment
                const pusherKey = '{{ env('PUSHER_APP_KEY') }}';
                const pusherCluster = '{{ env('PUSHER_APP_CLUSTER', 'mt1') }}';

                console.log('🔑 Pusher Key:', pusherKey);
                console.log('📍 Pusher Cluster:', pusherCluster);

                if (!pusherKey) {
                    throw new Error('Pusher APP_KEY not found in environment');
                }

                // Enable debug logging
                Pusher.logToConsole = true;

                // Initialize Pusher WITH CORRECT CREDENTIALS
                pusher = new Pusher(pusherKey, {
                    cluster: pusherCluster,
                    forceTLS: true,
                    encrypted: true
                });

                // Subscribe to channel
                const channelName = 'chat.room.{{ $chatRoom->id }}';
                console.log('📡 Subscribing to channel:', channelName);

                const channel = pusher.subscribe(channelName);

                // Listen for new messages
                channel.bind('ChatMessageSent', function (data) {
    if (data.message) {
        // Har user ke liye addMessageToChat
        addMessageToChat(data.message);

        // Agar current user ka message hai to notification mat dikhao
        if (data.message.user_id !== currentUserId) {
            playNotificationSound();
            showNotification(`New message from ${data.message.user.name}`);
        }
    }
        });

                // Connection events
                channel.bind('pusher:subscription_succeeded', function() {
                    console.log('✅ Successfully subscribed to Pusher channel');
                    updateConnectionStatus('connected');
                });

                channel.bind('pusher:subscription_error', function(status) {
                    console.error('❌ Pusher subscription error:', status);
                    updateConnectionStatus('error');
                });

                pusher.connection.bind('connected', function() {
                    console.log('✅ Pusher Connected!');
                    updateConnectionStatus('connected');
                });

                pusher.connection.bind('disconnected', function() {
                    console.log('❌ Pusher Disconnected');
                    updateConnectionStatus('error');
                });

                pusher.connection.bind('error', function(err) {
                    console.error('❌ Pusher Connection Error:', err);
                    updateConnectionStatus('error');
                });

                console.log('📡 Pusher initialization complete');

            } catch (error) {
                console.error('❌ Pusher initialization failed:', error);
                updateConnectionStatus('error');
                showNotification('Real-time connection failed: ' + error.message, 'error');
            }
        }

        // Add message to chat UI
        function addMessageToChat(message) {
            const isOwnMessage = message.user_id === currentUserId;

            console.log('🖥️ Rendering message:', message);

            const messageHtml = `
                <div class="flex items-start space-x-3 animate-fade-in ${isOwnMessage ? 'flex-row-reverse space-x-reverse' : ''}">
                    <div class="w-10 h-10 ${isOwnMessage ? 'bg-gradient-to-br from-green-500 to-emerald-600' : 'bg-gradient-to-br from-primary-500 to-blue-600'} rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0 shadow-lg"
                         title="${message.user.name} (${message.user.role})">
                        ${message.user.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 max-w-md ${isOwnMessage ? 'text-right' : ''}">
                        <div class="inline-block ${isOwnMessage ? 'bg-chat-sent' : 'bg-chat-received'} rounded-2xl px-4 py-3 shadow-lg border border-gray-200">
                            <div class="flex items-center space-x-2 mb-2 ${isOwnMessage ? 'flex-row-reverse space-x-reverse' : ''}">
                                <span class="text-sm font-bold ${isOwnMessage ? 'text-green-800' : 'text-primary-700'}">${message.user.name}</span>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">${formatTime(message.created_at)}</span>
                            </div>
                            <p class="text-gray-800 text-sm">${message.message}</p>
                            ${message.attachment ? `
                                <div class="mt-2">
                                    <a href="/storage/${message.attachment}"
                                       target="_blank"
                                       class="inline-flex items-center space-x-2 text-xs text-primary-600 hover:text-primary-700 bg-primary-50 rounded-lg px-3 py-2">
                                        <i class="fas fa-paperclip"></i>
                                        <span>${message.attachment_name}</span>
                                    </a>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('messages-list').insertAdjacentHTML('beforeend', messageHtml);

            // Auto-scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Format time
        function formatTime(dateString) {
            try {
                const date = new Date(dateString);
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            } catch (e) {
                return 'Just now';
            }
        }

        // Helper functions
        function playNotificationSound() {
            try {
                const audio = new Audio('data:audio/wav;base64,UklGRigAAABXQVZFZm10IBAAAAABAAEARKwAAIhYAQACABAAZGF0YQQAAAAAAA==');
                audio.volume = 0.3;
                audio.play().catch(e => console.log('Audio play failed'));
            } catch (e) {
                console.log('Audio not supported');
            }
        }

        function showNotification(message, type = 'info') {
            document.querySelectorAll('.chat-notification').forEach(el => el.remove());

            const notification = document.createElement('div');
            const bgColor = type === 'error' ? 'bg-red-500' : 'bg-green-500';

            notification.className = `chat-notification fixed top-4 right-4 ${bgColor} text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-fade-in`;
            notification.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas ${type === 'error' ? 'fa-exclamation-triangle' : 'fa-bell'}"></i>
                    <span class="text-sm">${message}</span>
                </div>
            `;

            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }

        function updateConnectionStatus(status) {
            const statusConfig = {
                'connected': { text: 'Live Connected', color: 'bg-green-500', icon: 'fa-wifi' },
                'error': { text: 'Connection Error', color: 'bg-red-500', icon: 'fa-exclamation-triangle' }
            };

            const config = statusConfig[status] || statusConfig.error;
            const statusElement = document.getElementById('connection-status');
            const connectionText = document.getElementById('connection-text');

            if (statusElement) {
                statusElement.className = `fixed bottom-4 left-4 ${config.color} text-white px-3 py-2 rounded-lg shadow-lg z-40`;
                statusElement.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i class="fas ${config.icon}"></i>
                        <span class="text-sm font-medium">${config.text}</span>
                    </div>
                `;
            }

            if (connectionText) {
                connectionText.textContent = config.text;
            }
        }

        // Start everything
        initializePusher();
        textarea.focus();

        // Debug functions
        window.debugChat = function() {
            console.log('=== CHAT DEBUG ===');
            console.log('Current User ID:', currentUserId);
            console.log('Chat Room ID:', '{{ $chatRoom->id }}');
            console.log('Pusher Key:', '{{ env('PUSHER_APP_KEY') }}');
            console.log('Pusher Cluster:', '{{ env('PUSHER_APP_CLUSTER', 'mt1') }}');
            console.log('Messages in DOM:', document.getElementById('messages-list').children.length);
        };
    });
</script>
</body>
</html>
