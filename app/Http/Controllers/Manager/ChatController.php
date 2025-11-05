<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\Projects; // Use Projects
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get project rooms where user is participant
        $projectRooms = ChatRoom::where('type', 'project')
            ->whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['project', 'messages' => function($query) {
                $query->latest()->limit(1);
            }, 'participants.user'])
            ->get();

        // Get direct message rooms
        $directRooms = ChatRoom::where('type', 'direct')
            ->whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['messages' => function($query) {
                $query->latest()->limit(1);
            }, 'participants.user'])
            ->get();

        // Get users for new DM - UPDATED FOR super_admin, admin, user
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            $availableUsers = User::where('id', '!=', $user->id)->get();
        } else {
            $availableUsers = User::whereIn('role', ['super_admin', 'admin'])->get();
        }

        return view('manager.chat.index', compact('projectRooms', 'directRooms', 'availableUsers'));
    }

    public function projectChat(Projects $project) // Use Projects
    {
        $user = auth()->user();

        // Check access based on roles
        if (!$user->canAccessProject($project)) {
            // Auto-add user to project team if not already (for testing)
            if (!$project->teamMembers->contains('id', $user->id)) {
                $project->teamMembers()->syncWithoutDetaching([$user->id]);
            }
        }

        // Find or create project chat room
        $chatRoom = ChatRoom::firstOrCreate(
            ['project_id' => $project->id, 'type' => 'project'],
            [
                'name' => $project->name . ' Chat',
                'description' => 'Project discussion group',
                'created_by' => $user->id
            ]
        );

        // Add current user to participants if not already
        $chatRoom->participants()->firstOrCreate(['user_id' => $user->id]);

        // Add all project members to chat room (manager + team members)
        $allMembers = $project->getAllMembers();

        foreach ($allMembers as $member) {
            $chatRoom->participants()->firstOrCreate(['user_id' => $member->id]);
        }

        $messages = $chatRoom->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        // Mark messages as read
        $this->markMessagesAsRead($chatRoom, $user);

        return view('manager.chat.project-chat', compact('project', 'chatRoom', 'messages'));
    }

    public function directChat(User $user)
    {
        $currentUser = auth()->user();

        // Check if current user can message this user - UPDATED
        if (!$currentUser->canMessage($user)) {
            abort(403);
        }

        // Find or create direct chat room
        $chatRoom = ChatRoom::where('type', 'direct')
            ->whereHas('participants', function($query) use ($currentUser, $user) {
                $query->where('user_id', $currentUser->id);
            })
            ->whereHas('participants', function($query) use ($currentUser, $user) {
                $query->where('user_id', $user->id);
            })
            ->first();

        if (!$chatRoom) {
            $chatRoom = ChatRoom::create([
                'name' => 'Direct Chat',
                'type' => 'direct',
                'created_by' => $currentUser->id
            ]);

            // Add both users as participants
            $chatRoom->participants()->createMany([
                ['user_id' => $currentUser->id],
                ['user_id' => $user->id]
            ]);
        }

        $messages = $chatRoom->messages()->with('user')->orderBy('created_at', 'asc')->paginate(50);

        // Mark messages as read
        $this->markMessagesAsRead($chatRoom, $currentUser);

        return view('manager.chat.direct-chat', compact('user', 'chatRoom', 'messages'));
    }

 public function sendMessage(Request $request, ChatRoom $chatRoom)
{
    $request->validate([
        'message' => 'required_without:attachment|string|max:1000',
        'attachment' => 'nullable|file|max:10240'
    ]);

    $user = auth()->user();

    // Check if user has access to this chat room
    if (!$user->canAccessChat($chatRoom)) {
        return response()->json(['error' => 'Access denied'], 403);
    }

    try {
        $messageData = [
            'chat_room_id' => $chatRoom->id,
            'user_id' => $user->id,
            'message' => $request->message ?: '[File Attachment]'
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('chat-attachments', 'public');
            $messageData['attachment'] = $path;
            $messageData['attachment_name'] = $file->getClientOriginalName();
        }

        $message = ChatMessage::create($messageData);
        $message->load('user');

        // DEBUG: Log before broadcasting
        \Log::info('Broadcasting message:', ['message_id' => $message->id, 'chat_room_id' => $chatRoom->id]);

        // Broadcast the event - PASS THE MESSAGE OBJECT, NOT ARRAY
        broadcast(new \App\Events\ChatMessageSent($message))->toOthers();

        // DEBUG: Log after broadcasting
        \Log::info('Message broadcast completed');

        // Prepare response data
        $responseData = [
            'id' => $message->id,
            'message' => $message->message,
            'user_id' => $message->user_id,
            'user' => [
                'id' => $message->user->id,
                'name' => $message->user->name,
                'role' => $message->user->role,
            ],
            'attachment' => $message->attachment,
            'attachment_name' => $message->attachment_name,
            'created_at' => $message->created_at->toISOString(),
            'chat_room_id' => $chatRoom->id
        ];

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'message_data' => $responseData
        ]);

    } catch (\Exception $e) {
        \Log::error('Message send error:', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'error' => 'Failed to send message: ' . $e->getMessage()
        ], 500);
    }
}

    private function markMessagesAsRead($chatRoom, $user)
    {
        $participant = $chatRoom->participants()->where('user_id', $user->id)->first();
        if ($participant) {
            $participant->update(['last_read_at' => now()]);
        }

        // Mark individual messages as read
        $chatRoom->messages()
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function markAsRead(ChatRoom $chatRoom)
    {
        $user = auth()->user();
        $this->markMessagesAsRead($chatRoom, $user);

        return response()->json(['success' => true]);
    }

    public function getMessages(ChatRoom $chatRoom)
    {
        $user = auth()->user();

        if (!$user->canAccessChat($chatRoom)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $messages = $chatRoom->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        return response()->json($messages);
    }
}
