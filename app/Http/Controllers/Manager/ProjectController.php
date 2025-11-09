<?php

namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Projects;
use App\Models\User;
use App\Models\Tasks;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // GET /projects
      public function index()
    {
        $user = auth()->user();

        // Get projects where user is manager
        $projects = Projects::where('manager_id', $user->id)
            ->with(['tasks', 'teamMembers'])
            ->latest()
            ->get();

        // Calculate statistics
        $totalTasks = Tasks::whereHas('project', function($query) use ($user) {
            $query->where('manager_id', $user->id);
        })->count();

        $teamMembersCount = User::whereHas('assignedTasks', function($query) use ($user) {
            $query->whereHas('project', function($q) use ($user) {
                $q->where('manager_id', $user->id);
            });
        })->distinct()->count();

        return view('manager.projects.index', compact('projects', 'totalTasks', 'teamMembersCount'));
    }


    // Running Projects
    public function running()
    {
        $projects = Projects::where('manager_id', auth()->id())
                       ->whereIn('status', ['pending', 'in_progress'])
                       ->withCount(['tasks'])
                       ->with(['tasks' => function($query) {
                           $query->select('project_id', 'status');
                       }])
                       ->latest()
                       ->get();

        // Calculate running projects statistics
        $runningStats = [
            'total' => $projects->count(),
            'in_progress' => $projects->where('status', 'in_progress')->count(),
            'pending' => $projects->where('status', 'pending')->count(),
            'total_tasks' => $projects->sum('tasks_count'),
            'completed_tasks' => $projects->sum(function($project) {
                return $project->tasks->where('status', 'done')->count();
            }),
            'overdue' => $projects->filter(function($project) {
                return $project->due_date && \Carbon\Carbon::parse($project->due_date)->isPast();
            })->count()
        ];

        return view('manager.projects.running', compact('projects', 'runningStats'));
    }

    // Completed Projects
    public function completed()
    {
        $projects = Projects::where('manager_id', auth()->id())
                       ->where('status', 'completed')
                       ->withCount(['tasks'])
                       ->with(['tasks' => function($query) {
                           $query->select('project_id', 'status');
                       }])
                       ->latest()
                       ->get();

        // Calculate completed projects statistics
        $completionStats = [
            'total' => $projects->count(),
            'completed_this_month' => $projects->where('updated_at', '>=', now()->subMonth())->count(),
            'completed_this_quarter' => $projects->where('updated_at', '>=', now()->subMonths(3))->count(),
            'total_tasks_completed' => $projects->sum('tasks_count'),
            'on_time' => $projects->filter(function($project) {
                return $project->due_date && \Carbon\Carbon::parse($project->due_date)->gte($project->updated_at);
            })->count()
        ];

        return view('manager.projects.completed', compact('projects', 'completionStats'));
    }

    // GET /projects/create


    // POST /projects
    public function show($id)
    {
        $project = Projects::where('manager_id', auth()->id())
                          ->with(['tasks', 'tasks.assignee', 'tasks.user', 'manager'])
                          ->withCount(['tasks'])
                          ->findOrFail($id);

        return view('manager.projects.show', compact('project'));
    }

    public function edit($id)
    {
        $project = Projects::where('manager_id', auth()->id())->findOrFail($id);
        return view('manager.projects.edit', compact('project'));
    }

    public function update(Request $request, $id)
    {
        $project = Projects::where('manager_id', auth()->id())->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'status' => $request->status,
        ]);

        return redirect()->route('manager.projects.show', $project->id)
                        ->with('success', 'Project updated successfully!');
    }

    public function destroy($id)
    {
        $project = Projects::where('manager_id', auth()->id())->findOrFail($id);
        $project->delete();

        return redirect()->route('manager.projects.index')
                        ->with('success', 'Project deleted successfully!');
    }

    // Mark project as completed
    public function markComplete($id)
    {
        $project = Projects::where('manager_id', auth()->id())->findOrFail($id);
        $project->update(['status' => 'completed']);

        return redirect()->back()
                        ->with('success', 'Project marked as completed!');
    }

    // Mark project as in progress
    public function markInProgress($id)
    {
        $project = Projects::where('manager_id', auth()->id())->findOrFail($id);
        $project->update(['status' => 'in_progress']);

        return redirect()->back()
            ->with('success', 'Project marked as in progress!');
    }

    public function updateStatus(Request $request, Projects $project)
{
    try {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        $project->update([
            'status' => $validated['status']
        ]);

        return redirect()->back()->with('success', 'Project status updated successfully');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update project status');
    }
}
}
