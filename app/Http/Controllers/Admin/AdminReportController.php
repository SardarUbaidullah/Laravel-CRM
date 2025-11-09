<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Projects as Project;
use App\Models\Tasks as Task;
use App\Models\User;
use App\Models\Teams as Team;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function quickStats()
    {
        try {
            $totalProjects = Project::count();
            $completedTasks = Task::where('status', 'done')->count();
            $activeTeam = User::whereIn('role', ['admin', 'user'])->count();
            $totalTasks = Task::count();
            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

            return response()->json([
                'total_projects' => $totalProjects,
                'completed_tasks' => $completedTasks,
                'active_team' => $activeTeam,
                'avg_performance' => $completionRate . '%',
                'completion_rate' => $completionRate
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'total_projects' => 0,
                'completed_tasks' => 0,
                'active_team' => 0,
                'avg_performance' => '0%',
                'completion_rate' => 0
            ]);
        }
    }

    public function getReportData($type)
    {
        try {
            switch ($type) {
                case 'progress':
                    $data = $this->getProgressData();
                    break;
                case 'workload':
                    $data = $this->getWorkloadData();
                    break;
                case 'performance':
                    $data = $this->getPerformanceData();
                    break;
                default:
                    return response()->json(['error' => 'Invalid report type'], 400);
            }

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load report data'], 500);
        }
    }

    private function getProgressData()
    {
        // Project Progress
        $totalProjects = Project::count();
        $completedProjects = Project::where('status', 'completed')->count();
        $inProgressProjects = Project::where('status', 'pending')->count();
        $planningProjects = Project::where('status', 'planning')->count();

        // Task Progress
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'done')->count();
        $inProgressTasks = Task::where('status', 'in_progress')->count();
        $todoTasks = Task::where('status', 'todo')->count();

        return [
            'projects' => [
                'total' => $totalProjects,
                'completed' => $completedProjects,
                'in_progress' => $inProgressProjects,
                'planning' => $planningProjects,
                'completion_rate' => $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 2) : 0
            ],
            'tasks' => [
                'total' => $totalTasks,
                'completed' => $completedTasks,
                'in_progress' => $inProgressTasks,
                'todo' => $todoTasks,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0
            ],
            'recent_projects' => Project::with('tasks')->latest()->take(5)->get()->map(function($project) {
                return [
                    'name' => $project->name,
                    'status' => $project->status,
                    'total_tasks' => $project->tasks->count(),
                    'completed_tasks' => $project->tasks->where('status', 'done')->count()
                ];
            })
        ];
    }

    private function getWorkloadData()
    {
        $users = User::whereIn('role', ['admin', 'user'])->get();

        $teamWorkload = $users->map(function($user) {
            $totalTasks = Task::where('assigned_to', $user->id)->count();
            $completedTasks = Task::where('assigned_to', $user->id)->where('status', 'done')->count();
            $inProgressTasks = Task::where('assigned_to', $user->id)->where('status', 'in_progress')->count();
            $todoTasks = Task::where('assigned_to', $user->id)->where('status', 'todo')->count();

            // Simple workload calculation
            $workloadLevel = $this->getWorkloadLevel($inProgressTasks);

            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'in_progress_tasks' => $inProgressTasks,
                'todo_tasks' => $todoTasks,
                'workload_level' => $workloadLevel,
            ];
        });

        return [
            'team_workload' => $teamWorkload,
            'summary' => [
                'total_team_members' => $users->count(),
                'total_assigned_tasks' => $teamWorkload->sum('total_tasks'),
                'total_completed' => $teamWorkload->sum('completed_tasks'),
                'total_in_progress' => $teamWorkload->sum('in_progress_tasks')
            ]
        ];
    }

    private function getPerformanceData()
    {
        $users = User::whereIn('role', ['admin', 'user'])->get();

        $userPerformance = $users->map(function($user) {
            $totalTasks = Task::where('assigned_to', $user->id)->count();
            $completedTasks = Task::where('assigned_to', $user->id)->where('status', 'done')->count();

            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'completion_rate' => $completionRate,
                'performance_level' => $this->getPerformanceLevel($completionRate)
            ];
        });

        // Overall quality metrics
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'done')->count();
        $inProgressTasks = Task::where('status', 'in_progress')->count();

        return [
            'user_performance' => $userPerformance,
            'quality_metrics' => [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'in_progress_tasks' => $inProgressTasks,
                'overall_completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
                'team_productivity' => $userPerformance->avg('completion_rate') ?? 0
            ]
        ];
    }

    private function getWorkloadLevel($inProgressTasks)
    {
        if ($inProgressTasks == 0) return 'Low';
        if ($inProgressTasks <= 3) return 'Normal';
        if ($inProgressTasks <= 6) return 'High';
        return 'Overloaded';
    }

    private function getPerformanceLevel($completionRate)
    {
        if ($completionRate >= 90) return 'Excellent';
        if ($completionRate >= 75) return 'Very Good';
        if ($completionRate >= 60) return 'Good';
        if ($completionRate >= 40) return 'Average';
        return 'Needs Improvement';
    }
}
