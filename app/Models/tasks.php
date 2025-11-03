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

    public function project()
    {
        return $this->belongsTo(Projects::class);
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
        return $this->hasMany(TaskSubtask::class);
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


        public function assignedTasks()
{
    return $this->belongsTo(User::class, 'manager_id');
}

}
