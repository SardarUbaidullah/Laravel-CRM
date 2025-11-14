<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Models\User;
use App\Models\Client;
use App\Models\Tasks;
use App\Models\Milestones;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // GET /projects
    public function index()
    {
        $projects = Projects::with(['tasks', 'teamMembers', 'milestones'])->latest()->get();

        // Calculate statistics
        $totalTasks = Tasks::count();
        $teamMembersCount = User::whereHas('assignedTasks')->distinct()->count();
        $totalMilestones = Milestones::count();

        return view('projects.index', compact('projects', 'totalTasks', 'teamMembersCount', 'totalMilestones'));
    }

    // Running Projects
    public function running()
    {
        $projects = Projects::whereIn('status', ['pending', 'in_progress'])
                       ->withCount(['tasks', 'milestones'])
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
            'total_milestones' => $projects->sum('milestones_count'),
            'overdue' => $projects->filter(function($project) {
                return $project->due_date && \Carbon\Carbon::parse($project->due_date)->isPast();
            })->count()
        ];

        return view('admin.projects.running', compact('projects', 'runningStats'));
    }

    // Completed Projects
    public function completed()
    {
        $projects = Projects::where('status', 'completed')
                       ->withCount(['tasks', 'milestones'])
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
            'total_milestones_completed' => $projects->sum('milestones_count'),
            'on_time' => $projects->filter(function($project) {
                return $project->due_date && \Carbon\Carbon::parse($project->due_date)->gte($project->updated_at);
            })->count()
        ];

        return view('admin.projects.completed', compact('projects', 'completionStats'));
    }

    // GET /projects/create
    public function create()
    {
        $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
        $clients = Client::active()->get();
        $teamMembers = User::where('role', 'user')->get();

        return view('projects.create', compact('managers', 'clients', 'teamMembers'));
    }

    // POST /projects
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'manager_id' => 'required|exists:users,id',
        ]);

        $project = Projects::create([
            'team_id' => $request->team_id,
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'manager_id' => $request->manager_id,
            'client_id' => $request->client_id,
            'status' => $request->status ?? 'pending',
        ]);

        $project->createProjectChatRoom();

        // Add team members if selected
        if ($request->has('team_members')) {
            foreach ($request->team_members as $memberId) {
                $project->teamMembers()->attach($memberId);
            }
        }

        return redirect()->route('projects.index')->with('success', 'Project created successfully');
    }

    // GET /projects/{id}
    public function show($id)
    {
        $project = Projects::with([
            'tasks',
            'tasks.assignee',
            'tasks.creator',
            'manager',
            'client',
            'milestones',
            'milestones.tasks',
            'teamMembers'
        ])->findOrFail($id);

        return view('projects.show', compact('project'));
    }

    // GET /projects/{id}/edit
    public function edit($id)
    {
        $project = Projects::with(['teamMembers'])->findOrFail($id);
        $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
        $clients = Client::active()->get();
        $teamMembers = User::where('role', 'user')->get();

        return view('projects.edit', compact('project', 'managers', 'clients', 'teamMembers'));
    }

    // PUT /projects/{id}
    public function update(Request $request, $id)
    {
        $project = Projects::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'required|exists:users,id',
            'client_id' => 'required|exists:clients,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'manager_id' => $request->manager_id,
            'client_id' => $request->client_id,
            'status' => $request->status,
        ]);

        // Sync team members
        if ($request->has('team_members')) {
            $project->teamMembers()->sync($request->team_members);
        } else {
            $project->teamMembers()->detach();
        }

        return redirect()->route('projects.show', $project->id)
                        ->with('success', 'Project updated successfully!');
    }

    // DELETE /projects/{id}
    public function destroy($id)
    {
        $project = Projects::findOrFail($id);
        $project->delete();

        return redirect()->route('projects.index')
                        ->with('success', 'Project deleted successfully!');
    }

    // Mark project as completed
    public function markComplete($id)
    {
        $project = Projects::findOrFail($id);
        $project->update(['status' => 'completed']);

        return redirect()->back()
                        ->with('success', 'Project marked as completed!');
    }

    // Mark project as in progress
    public function markInProgress($id)
    {
        $project = Projects::findOrFail($id);
        $project->update(['status' => 'in_progress']);

        return redirect()->back()
                        ->with('success', 'Project marked as in progress!');
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $project = Projects::findOrFail($id);

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
