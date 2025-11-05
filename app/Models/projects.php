<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Projects  extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'name',
        'description',
        'start_date',
        'due_date',
        'status',
        'created_by',
        'manager_id',
    ];

    protected $dates = [
        'start_date',
        'due_date',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // Project members pivot
    public function members()
    {
        return $this->belongsToMany(User::class, 'project_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(tasks::class , 'project_id');
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function manager()
{
    return $this->belongsTo(User::class, 'manager_id');
}




  // Manager relationship

    // Team members relationship - FIXED


    // Tasks relationship


    // Chat rooms relationship
    public function chatRooms()
    {
        return $this->hasMany(ChatRoom::class);
    }

    // Get all users associated with project (manager + team members)

    // Helper methods
    public function getProgressAttribute()
    {
        $totalTasks = $this->tasks->count();
        $completedTasks = $this->tasks->where('status', 'done')->count();

        return $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast();
    }

    public function hasTeamMember($userId)
    {
        return $this->teamMembers->contains('id', $userId);
    }

     public function hasAccess($userId)
    {
        $user = User::find($userId);
        if (!$user) return false;

        if ($user->isAdmin()) return true;

        if ($user->isManager()) {
            return $this->manager_id === $user->id ||
                   $this->teamMembers->contains('id', $user->id);
        }

        return $this->teamMembers->contains('id', $user->id);
    }





    public function teamMembers()
    {
        return $this->belongsToMany(User::class, 'project_team_members', 'project_id', 'user_id')
                    ->withTimestamps();
    }





    public function getAllMembers()
    {
        $members = $this->teamMembers;

        if ($this->manager && !$members->contains('id', $this->manager->id)) {
            $members->push($this->manager);
        }

        return $members;
    }
}
