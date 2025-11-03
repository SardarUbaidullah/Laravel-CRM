<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Projects;
use App\Models\Tasks;
use App\Models\User;
use App\Models\TimeLogs;
use App\Models\TaskSubtasks;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get projects based on user role
        if ($user->role === 'admin') {
            $projects = Projects::where('manager_id', $user->id)->get();
        } elseif ($user->role === 'super_admin') {
            $projects = Projects::all();
        } else {
            $projects = Projects::whereHas('tasks', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })->get();
        }

        // Get statistics
        $activeProjects = $projects->where('status', 'in_progress')->count();
        $pendingTasks = Tasks::whereIn('project_id', $projects->pluck('id'))
                            ->where('status', '!=', 'done')
                            ->count();

        // Get team members (users assigned to tasks in manager's projects)
        $teamMembers = User::whereHas('assignedTasks', function ($q) use ($projects) {
            $q->whereIn('project_id', $projects->pluck('id'));
        })->distinct()->get();

        // Calculate completion rate
        $totalTasks = Tasks::whereIn('project_id', $projects->pluck('id'))->count();
        $completedTasks = Tasks::whereIn('project_id', $projects->pluck('id'))
                              ->where('status', 'done')
                              ->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // Get recent activities
        $recentTasks = Tasks::whereIn('project_id', $projects->pluck('id'))
                           ->with('user')
                           ->latest()
                           ->take(5)
                           ->get();

        // Get upcoming deadlines
        $upcomingDeadlines = Tasks::whereIn('project_id', $projects->pluck('id'))
                                 ->where('due_date', '>=', now())
                                 ->where('status', '!=', 'done')
                                 ->with('project')
                                 ->orderBy('due_date')
                                 ->take(5)
                                 ->get();

        return view('manager.dashboard', compact(
            'projects',
            'activeProjects',
            'pendingTasks',
            'teamMembers',
            'completionRate',
            'recentTasks',
            'upcomingDeadlines',
            'completedTasks',
            'totalTasks'
        ));
    }
}
