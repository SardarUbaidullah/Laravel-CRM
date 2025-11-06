<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tasks;
use App\Models\Projects;

class TeamOwnController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Calculate user stats
        $totalTasks = Tasks::where('assigned_to', $user->id)->count();
        $completedTasks = Tasks::where('assigned_to', $user->id)->where('status', 'done')->count();
        $pendingTasks = Tasks::where('assigned_to', $user->id)->whereIn('status', ['todo', 'in_progress'])->count();

        $userStats = [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
        ];

        // Get recent tasks
        $tasks = Tasks::where('assigned_to', $user->id)
            ->with('project')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get upcoming deadlines
        $upcomingDeadlines = Tasks::where('assigned_to', $user->id)
            ->whereIn('status', ['todo', 'in_progress'])
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->orderBy('due_date', 'asc')
            ->limit(3)
            ->get();

        return view('team.index', compact('userStats', 'tasks', 'upcomingDeadlines'));
    }

    public function tasks()
    {
        $tasks = Tasks::where('assigned_to', Auth::id())
            ->with('project')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('team.tasks', compact('tasks'));
    }

    public function projects()
    {
        $projects = Projects::whereHas('tasks', function($query) {
            $query->where('assigned_to', Auth::id());
        })->with(['tasks' => function($query) {
            $query->where('assigned_to', Auth::id());
        }])->get();

        return view('team.projects', compact('projects'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('team.profile', compact('user'));
    }
    // Add this method to TeamOwnController
public function showTask($id)
{
    $task = Tasks::where('assigned_to', Auth::id())
        ->with('project')
        ->findOrFail($id);

    return view('team.task-show', compact('task'));
}
}
