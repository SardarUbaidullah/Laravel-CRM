<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tasks extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'assigned_to',
        'created_by',
        'title',
        'description',
        'priority',
        'status',
        'start_date',
        'due_date',
    ];

    protected $dates = [
        'start_date',
        'due_date',
    ];
    protected $casts = [
    'due_date' => 'datetime',
];


   public function project()
{
    return $this->belongsTo(Projects::class, 'project_id');
}


    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

public function subtasks()
{
    return $this->hasMany(task_subtasks::class, 'task_id');
}


    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class);
    }
 public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

        public function assignedTasks()
{
    return $this->belongsTo(User::class, 'manager_id');
}




    // Assignee relationship


    // Creator relationship


    // Helper methods
    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast();
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'gray',
            default => 'gray',
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'done' => 'green',
            'in_progress' => 'blue',
            'todo' => 'gray',
            default => 'gray',
        };
    }
}
