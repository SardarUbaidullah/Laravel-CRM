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
  public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function canAccessProject($project)
    {
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return true;
        }

        return $project->teamMembers->contains('id', $this->id);
    }

    public function canMessage($user)
    {
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return true;
        }

        // For team members, they can only message their managers and super_admin
        if ($this->isUser()) {
            if ($user->isSuperAdmin()) {
                return true;
            }

            if ($user->isAdmin()) {
                // Check if this admin manages any project where the user is a team member
                return $user->managedProjects()
                    ->whereHas('teamMembers', function($query) {
                        $query->where('user_id', $this->id);
                    })
                    ->exists();
            }

            return false;
        }

        return true;
    }

    public function canAccessChat($chatRoom)
    {
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return true;
        }

        if ($chatRoom->type === 'project') {
            return $chatRoom->project && $chatRoom->project->teamMembers->contains('id', $this->id);
        }

        if ($chatRoom->type === 'direct') {
            return $chatRoom->participants->contains('id', $this->id);
        }

        return false;
    }

    // Relationship for projects managed by this user (for admins)
   
}
