<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function managedProjects()
    {
        return $this->hasMany(Projects::class, 'manager_id');
    }

    public function assignedTasks()
    {
        return $this->hasMany(tasks::class, 'assigned_to');
    }

    public function createdTasks()
    {
        return $this->hasMany(tasks::class, 'created_by');
    }

    // Chat Relationships
    public function chatRooms()
    {
        return $this->belongsToMany(ChatRoom::class, 'chat_participants', 'user_id', 'chat_room_id')
                    ->withTimestamps();
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function chatParticipants()
    {
        return $this->hasMany(ChatParticipant::class);
    }

    // Project Relationships
    public function teamProjects()
    {
        return $this->belongsToMany(Projects::class, 'project_team_members', 'user_id', 'project_id')
                    ->withTimestamps();
    }

    // Helper Methods - UPDATED FOR super_admin, admin, user
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin' || $this->role === 'super_admin';
    }

    public function isManager()
    {
        // If you don't have manager role, use admin as manager
        return $this->role === 'admin' || $this->role === 'super_admin';
    }

    public function isRegularUser()
    {
        return $this->role === 'user';
    }

    public function canAccessProject($project)
    {
        // Super admin and admin can access everything
        if ($this->isAdmin() || $this->isSuperAdmin()) {
            return true;
        }

        // Regular users can only access projects they're team members of
        return $project->teamMembers->contains('id', $this->id);
    }

    public function canAccessChat($chatRoom)
    {
        // Super admin and admin can access everything
        if ($this->isAdmin() || $this->isSuperAdmin()) {
            return true;
        }

        // For project chats, check if user is part of the project
        if ($chatRoom->isProjectChat()) {
            return $chatRoom->project && (
                $chatRoom->project->manager_id === $this->id ||
                $chatRoom->project->teamMembers->contains('id', $this->id) ||
                $chatRoom->participants->contains('user_id', $this->id)
            );
        }

        // For direct chats, check if user is a participant
        if ($chatRoom->isDirectChat()) {
            return $chatRoom->participants->contains('user_id', $this->id);
        }

        return false;
    }

    public function canMessage($user)
    {
        if ($this->id === $user->id) return false;

        // Super admin and admin can message anyone
        if ($this->isAdmin() || $this->isSuperAdmin()) return true;

        // Regular users can only message admins and super admins
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    // Get all projects user is involved in (managed + team member)
    public function getAllProjectsAttribute()
    {
        $managed = $this->managedProjects;
        $team = $this->teamProjects;

        return $managed->merge($team)->unique('id');
    }
}
