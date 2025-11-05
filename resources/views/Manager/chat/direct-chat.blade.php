<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with {{ $user->name }} - CRM</title>
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
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('manager.chat.index') }}"
                   class="text-gray-500 hover:text-primary-600 transition-colors duration-200">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                        <p class="text-gray-600 flex items-center">
                            <span class="capitalize">{{ $user->role }}</span>
                            <span class="w-2 h-2 bg-green-500 rounded-full ml-2 animate-pulse" title="Online"></span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button class="w-10 h-10 bg-white border border-gray-300 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-phone-alt"></i>
                </button>
                <button class="w-10 h-10 bg-white border border-gray-300 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-video"></i>
                </button>
                <button class="w-10 h-10 bg-white border border-gray-300 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </div>

        <!-- Chat Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[600px]">
            <!-- Chat Header -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-white">Direct Message</h2>
                            <p class="text-green-100 text-sm">Private conversation</p>
                        </div>
                    </div>
                    <div class="text-green-100 text-sm">
                        <i class="fas fa-circle text-xs mr-1"></i>
                        Active now
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-1 p-6 overflow-y-auto bg-gray-50" id="messages-container">
                <div class="space-y-4">
                    @foreach($messages->reverse() as $message)
                    <div class="flex items-start space-x-3 {{ $message->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                        <div class="w-8 h-8 {{ $message->user_id === auth()->id() ? 'bg-gradient-to-r from-green-500 to-green-600' : 'bg-gradient-to-r from-primary-500 to-primary-600' }} rounded-full flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                            {{ strtoupper(substr($message->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 max-w-xs {{ $message->user_id === auth()->id() ? 'text-right' : '' }}">
                            <div class="inline-block bg-white rounded-2xl px-4 py-3 shadow-sm border border-gray-200">
                                <p class="text-gray-800 text-sm">{{ $message->message }}</p>
                                @if($message->attachment)
                                <div class="mt-2">
                                    <a href="{{ Storage::url($message->attachment) }}"
                                       target="_blank"
                                       class="inline-flex items-center space-x-2 text-xs text-primary-600 hover:text-primary-700 bg-primary-50 rounded-lg px-3 py-2">
                                        <i class="fas fa-paperclip"></i>
                                        <span>{{ $message->attachment_name }}</span>
                                    </a>
                                </div>
                                @endif
                                <div class="text-xs text-gray-500 mt-1">{{ $message->formatted_time }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Message Input -->
            <div class="border-t border-gray-200 p-4 bg-white">
                <form id="message-form" class="flex space-x-3">
                    @csrf
                    <div class="flex-1">
                        <textarea
                            id="message-input"
                            name="message"
                            rows="1"
                            placeholder="Type your message..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-2xl resize-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            required
                        ></textarea>
                    </div>
                    <div class="flex space-x-2">
                        <label for="attachment" class="cursor-pointer">
                            <input type="file" id="attachment" name="attachment" class="hidden">
                            <span class="w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center text-gray-600 transition-colors duration-200">
                                <i class="fas fa-paperclip"></i>
                            </span>
                        </label>
                        <button
                            type="submit"
                            class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-xl flex items-center justify-center text-white transition-all duration-200 shadow-md hover:shadow-lg"
                        >
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom
        const messagesContainer = document.getElementById('messages-container');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        // Auto-resize textarea
        const textarea = document.getElementById('message-input');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Handle form submission
        document.getElementById('message-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const message = formData.get('message').trim();

            if (!message && !formData.get('attachment')) return;

            fetch('{{ route('manager.chat.send', $chatRoom) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.reset();
                    textarea.style.height = 'auto';
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Real-time functionality with Pusher
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true
        });

        const channel = pusher.subscribe('chat.room.{{ $chatRoom->id }}');
        channel.bind('message.sent', function(data) {
            // Add new message to the chat
            const message = data.message;
            const isOwnMessage = message.user_id === {{ auth()->id() }};

            const messageHtml = `
                <div class="flex items-start space-x-3 ${isOwnMessage ? 'flex-row-reverse space-x-reverse' : ''}">
                    <div class="w-8 h-8 ${isOwnMessage ? 'bg-gradient-to-r from-green-500 to-green-600' : 'bg-gradient-to-r from-primary-500 to-primary-600'} rounded-full flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                        ${message.user.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 max-w-xs ${isOwnMessage ? 'text-right' : ''}">
                        <div class="inline-block bg-white rounded-2xl px-4 py-3 shadow-sm border border-gray-200">
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
                            <div class="text-xs text-gray-500 mt-1 ${isOwnMessage ? 'text-right' : ''}">
                                ${new Date(message.created_at).toLocaleString()}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.querySelector('#messages-container .space-y-4').insertAdjacentHTML('beforeend', messageHtml);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });

        // Focus on message input when page loads
        textarea.focus();
    </script>
</body>
</html>
