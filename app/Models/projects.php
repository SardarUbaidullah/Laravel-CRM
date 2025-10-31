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
        return $this->hasMany(Task::class);
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
}
