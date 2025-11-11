<?php

namespace App\Http\Controllers;

use App\Models\Milestones ;
use App\Models\Projects ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MilestoneController extends Controller
{
    /**
     * Display a listing of milestones.
     */
   public function index(Request $request)
{
    // Check if user is super_admin
    $isSuperAdmin = auth()->user()->role === 'super_admin';

    // Base query - different logic for super_admin vs manager
    if ($isSuperAdmin) {
        $query = Milestones::with(['project', 'tasks']);
        $projectsQuery = Projects::query(); // All projects for super_admin
    } else {
        $query = Milestones::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->with(['project', 'tasks']);
        $projectsQuery = Projects::where('manager_id', auth()->id()); // Only manager's projects
    }

    // Filter by project if provided
    if ($request->has('project_id') && $request->project_id) {
        $query->where('project_id', $request->project_id);
    }

    // Filter by status if provided
    if ($request->has('status') && $request->status) {
        $query->where('status', $request->status);
    }

    // Search functionality
    if ($request->has('search') && $request->search) {
        $query->where(function($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%');
        });
    }

    $milestones = $query->latest()->paginate(10);
    $projects = $projectsQuery->get();
    $selectedProject = $request->has('project_id') ? Projects::find($request->project_id) : null;

    // Get milestone statistics based on role
    if ($isSuperAdmin) {
        $milestoneStats = [
            'total' => Milestones::count(),
            'completed' => Milestones::where('status', 'completed')->count(),
            'in_progress' => Milestones::where('status', 'in_progress')->count(),
            'pending' => Milestones::where('status', 'pending')->count(),
        ];
    } else {
        $milestoneStats = [
            'total' => Milestones::whereHas('project', function($query) {
                $query->where('manager_id', auth()->id());
            })->count(),
            'completed' => Milestones::whereHas('project', function($query) {
                $query->where('manager_id', auth()->id());
            })->where('status', 'completed')->count(),
            'in_progress' => Milestones::whereHas('project', function($query) {
                $query->where('manager_id', auth()->id());
            })->where('status', 'in_progress')->count(),
            'pending' => Milestones::whereHas('project', function($query) {
                $query->where('manager_id', auth()->id());
            })->where('status', 'pending')->count(),
        ];
    }

    return view('admin.milestones.index', compact(
        'milestones',
        'projects',
        'selectedProject',
        'milestoneStats',
        'isSuperAdmin'
    ));
}

    /**
     * Show the form for creating a new milestone.
     */
    public function create()
    {
        $projects = Projects::where('status', '!=', 'completed')
                          ->orWhereNull('status')
                          ->latest()
                          ->get();

        // Auto-select project if coming from project page
        $selectedProjectId = request('project_id');

        return view('admin.milestones.create', compact('projects', 'selectedProjectId'));
    }

    /**
     * Store a newly created milestone in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after_or_equal:today',
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                Milestones::create($validated);
            });

            $redirectRoute = $request->has('redirect_to_project')
                ? route('manager.projects.show', $validated['project_id'])
                : route('manager.milestones.index', ['project_id' => $validated['project_id']]);

            return redirect($redirectRoute)
                ->with('success', 'Milestone created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create milestone. Please try again.');
        }
    }

    /**
     * Display the specified milestone.
     */
    public function show($id)
    {
        $milestone = Milestones::with(['project', 'tasks.assignee', 'tasks.subtasks'])
                            ->findOrFail($id);

        // Calculate progress
        $totalTasks = $milestone->tasks->count();
        $completedTasks = $milestone->tasks->where('status', 'done')->count();
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        return view('admin.milestones.show', compact('milestone', 'progress', 'totalTasks', 'completedTasks'));
    }

    /**
     * Show the form for editing the specified milestone.
     */
    public function edit($id)
    {
        $milestone = Milestones::findOrFail($id);
        $projects = Project::where('status', '!=', 'completed')
                          ->orWhereNull('status')
                          ->latest()
                          ->get();

        return view('admin.milestones.edit', compact('milestone', 'projects'));
    }

    /**
     * Update the specified milestone in storage.
     */
    public function update(Request $request, $id)
    {
        $milestone = Milestones::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        try {
            DB::transaction(function () use ($milestone, $validated) {
                $milestone->update($validated);

                // If milestone is completed, update related tasks
                if ($validated['status'] === 'completed') {
                    $milestone->tasks()->where('status', '!=', 'done')->update(['status' => 'done']);
                }
            });

            $redirectRoute = $request->has('redirect_to_project')
                ? route('manager.projects.show', $milestone->project_id)
                : route('manager.milestones.index', ['project_id' => $milestone->project_id]);

            return redirect($redirectRoute)
                ->with('success', 'Milestone updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update milestone. Please try again.');
        }
    }

    /**
     * Remove the specified milestone from storage.
     */
    public function destroy($id)
    {
        $milestone = Milestones::findOrFail($id);
        $projectId = $milestone->project_id;

        try {
            DB::transaction(function () use ($milestone) {
                // Detach tasks from milestone before deletion
                $milestone->tasks()->update(['milestone_id' => null]);
                $milestone->delete();
            });

            return redirect()->route('manager.milestones.index', ['project_id' => $projectId])
                ->with('success', 'Milestone deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete milestone. Please try again.');
        }
    }

    /**
     * Update milestone status via AJAX
     */
    public function updateStatus(Request $request, $id)
    {
        $milestone = Milestones::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        $milestone->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Milestone status updated successfully!',
            'status' => $milestone->status
        ]);
    }

    /**
     * Get milestones for a specific project (AJAX)
     */
    public function getProjectMilestones($projectId)
    {
        $milestones = Milestones::where('project_id', $projectId)
                              ->where('status', '!=', 'completed')
                              ->get(['id', 'title']);

        return response()->json($milestones);
    }

    /**
     * Bulk update milestones status
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'milestone_ids' => 'required|array',
            'milestone_ids.*' => 'exists:milestones,id',
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        try {
            Milestones::whereIn('id', $request->milestone_ids)
                    ->update(['status' => $request->status]);

            return redirect()->back()
                ->with('success', 'Selected milestones updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update milestones. Please try again.');
        }
    }
}
