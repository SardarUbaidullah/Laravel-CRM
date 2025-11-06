<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\Projects;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'user') {
            // For team members: only show project rooms where they are team members
            $projectRooms = ChatRoom::where('type', 'project')
                ->whereHas('participants', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->whereHas('project', function($query) use ($user) {
                    $query->whereHas('teamMembers', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                })
                ->with(['project', 'messages' => function($query) {
                    $query->latest()->limit(1);
                }, 'participants.user'])
                ->get();

            // Get direct message rooms where user is participant
            $directRooms = ChatRoom::where('type', 'direct')
                ->whereHas('participants', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['messages' => function($query) {
                    $query->latest()->limit(1);
                }, 'participants.user'])
                ->get();

            // Get available users for new DM (managers and super_admin only)
            $availableUsers = User::where(function($query) use ($user) {
                // Get managers of projects where user is team member
                $query->whereHas('managedProjects', function($q) use ($user) {
                    $q->whereHas('teamMembers', function($teamQuery) use ($user) {
                        $teamQuery->where('user_id', $user->id);
                    });
                })
                ->orWhere('role', 'super_admin');
            })
            ->where('id', '!=', $user->id)
            ->whereIn('role', ['admin', 'super_admin'])
            ->get();

        } else {
            // Original logic for admin/super_admin
            $projectRooms = ChatRoom::where('type', 'project')
                ->whereHas('participants', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['project', 'messages' => function($query) {
                    $query->latest()->limit(1);
                }, 'participants.user'])
                ->get();

            $directRooms = ChatRoom::where('type', 'direct')
                ->whereHas('participants', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['messages' => function($query) {
                    $query->latest()->limit(1);
                }, 'participants.user'])
                ->get();

            if ($user->isAdmin() || $user->isSuperAdmin()) {
                $availableUsers = User::where('id', '!=', $user->id)->get();
            } else {
                $availableUsers = User::whereIn('role', ['super_admin', 'admin'])->get();
            }
        }

        return view('manager.chat.index', compact('projectRooms', 'directRooms', 'availableUsers'));
    }

    public function projectChat(Projects $project)
    {
        $user = auth()->user();

        // Role-based access control
        if ($user->role === 'user') {
            // Check if user is team member of this project
            if (!$project->teamMembers->contains('id', $user->id)) {
                abort(403, 'Access denied. You are not a team member of this project.');
            }
        } else {
            // Original access check for admin/super_admin
            if (!$user->canAccessProject($project)) {
                if (!$project->teamMembers->contains('id', $user->id)) {
                    $project->teamMembers()->syncWithoutDetaching([$user->id]);
                }
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

        // Add all project members to chat room
        $allMembers = $project->getAllMembers();
        foreach ($allMembers as $member) {
            $chatRoom->participants()->firstOrCreate(['user_id' => $member->id]);
        }

        $messages = $chatRoom->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        $this->markMessagesAsRead($chatRoom, $user);

        return view('manager.chat.project-chat', compact('project', 'chatRoom', 'messages'));
    }

    public function directChat(User $user)
    {
        $currentUser = auth()->user();

        // Enhanced role-based access control for direct messaging
        if ($currentUser->role === 'user') {
            // Team members can only message their managers and super_admin
            $canMessage = false;

            // Check if target user is super_admin
            if ($user->role === 'super_admin') {
                $canMessage = true;
            }
            // Check if target user is manager of any project where current user is team member
            elseif ($user->role === 'admin') {
                $canMessage = $user->managedProjects()
                    ->whereHas('teamMembers', function($query) use ($currentUser) {
                        $query->where('user_id', $currentUser->id);
                    })
                    ->exists();
            }

            if (!$canMessage) {
                abort(403, 'You can only message your project managers and super administrators.');
            }
        } else {
            // Original logic for admin/super_admin
            if (!$currentUser->canMessage($user)) {
                abort(403);
            }
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

            $chatRoom->participants()->createMany([
                ['user_id' => $currentUser->id],
                ['user_id' => $user->id]
            ]);
        }

        $messages = $chatRoom->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

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

        // Enhanced access control based on user role
        if ($user->role === 'user') {
            if ($chatRoom->type === 'project') {
                // Check if user is team member of the project
                if (!$chatRoom->project || !$chatRoom->project->teamMembers->contains('id', $user->id)) {
                    return response()->json(['error' => 'Access denied'], 403);
                }
            } else if ($chatRoom->type === 'direct') {
                // Check if user is participant in this direct chat
                $isParticipant = $chatRoom->participants()->where('user_id', $user->id)->exists();
                if (!$isParticipant) {
                    return response()->json(['error' => 'Access denied'], 403);
                }
            }
        } else {
            // Original access check for admin/super_admin
            if (!$user->canAccessChat($chatRoom)) {
                return response()->json(['error' => 'Access denied'], 403);
            }
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

            \Log::info('Broadcasting message:', $responseData);
            broadcast(new \App\Events\ChatMessageSent($responseData))->toOthers();
            \Log::info('Message broadcast completed');

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

        // Enhanced access control
        if ($user->role === 'user') {
            if ($chatRoom->type === 'project') {
                if (!$chatRoom->project || !$chatRoom->project->teamMembers->contains('id', $user->id)) {
                    return response()->json(['error' => 'Access denied'], 403);
                }
            } else if ($chatRoom->type === 'direct') {
                $isParticipant = $chatRoom->participants()->where('user_id', $user->id)->exists();
                if (!$isParticipant) {
                    return response()->json(['error' => 'Access denied'], 403);
                }
            }
        } else {
            if (!$user->canAccessChat($chatRoom)) {
                return response()->json(['error' => 'Access denied'], 403);
            }
        }

        $messages = $chatRoom->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        return response()->json($messages);
    }
}
