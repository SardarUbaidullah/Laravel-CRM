<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class files extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'task_id',
        'user_id',
        'file_name',
        'file_path',
        'version',
    ];

    public function project()
    {
        return $this->belongsTo(Projects::class);
    }

    public function task()
    {
        return $this->belongsTo(tasks::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
