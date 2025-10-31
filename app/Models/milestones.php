<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class milestones extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'due_date',
        'status',
    ];

    protected $dates = [
        'due_date',
    ];

    public function project()
    {
        return $this->belongsTo(Projects::class);
    }
}
