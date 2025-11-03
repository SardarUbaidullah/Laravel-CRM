<?php

namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Projects;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // GET /projects
    public function index()
    {
        $projects = Projects::where('manager_id', auth()->id())
                       ->withCount(['tasks'])
                       ->with(['tasks' => function($query) {
                           $query->select('project_id', 'status');
                       }])
                       ->latest()
                       ->get();

    // Calculate additional statistics
    $totalTasks = \App\Models\Tasks::whereHas('project', function($query) {
        $query->where('manager_id', auth()->id());
    })->count();

    $teamMembersCount = \App\Models\User::whereHas('assignedTasks', function($query) {
        $query->whereHas('project', function($q) {
            $q->where('manager_id', auth()->id());
        });
    })->distinct()->count();

    return view('manager.projects.index', compact('projects', 'totalTasks', 'teamMembersCount'));
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

    // DELETE /projects/{id}

}
