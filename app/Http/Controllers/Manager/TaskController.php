<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Tasks;
use App\Models\Projects;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->with(['project', 'user', 'assignee']);

        // Filter by project if provided
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $tasks = $query->latest()->get();
        $projects = Projects::where('manager_id', auth()->id())->get();

        // Get counts for different statuses
        $taskCounts = [
            'all' => Tasks::whereHas('project', function($query) {
                $query->where('manager_id', auth()->id());
            })->count(),
            'pending' => Tasks::whereHas('project', function($query) {
                $query->where('manager_id', auth()->id());
            })->whereIn('status', ['todo', 'in_progress'])->count(),
            'completed' => Tasks::whereHas('project', function($query) {
                $query->where('manager_id', auth()->id());
            })->where('status', 'done')->count(),
            'todo' => Tasks::whereHas('project', function($query) {
                $query->where('manager_id', auth()->id());
            })->where('status', 'todo')->count(),
            'in_progress' => Tasks::whereHas('project', function($query) {
                $query->where('manager_id', auth()->id());
            })->where('status', 'in_progress')->count(),
        ];

        $currentStatus = $request->get('status', 'all');

        return view('manager.tasks.index', compact('tasks', 'projects', 'taskCounts', 'currentStatus'));
    }

    public function pendingTasks(Request $request)
    {
        $query = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->whereIn('status', ['todo', 'in_progress'])
          ->with(['project', 'user', 'assignee']);

        // Filter by project if provided
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        $tasks = $query->latest()->get();
        $projects = Projects::where('manager_id', auth()->id())->get();

        return view('manager.tasks.pending', compact('tasks', 'projects'));
    }

    public function completedTasks(Request $request)
    {
        $query = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->where('status', 'done')
          ->with(['project', 'user', 'assignee']);

        // Filter by project if provided
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        $tasks = $query->latest()->get();
        $projects = Projects::where('manager_id', auth()->id())->get();

        return view('manager.tasks.completed', compact('tasks', 'projects'));
    }

    public function create(Request $request)
    {
        $projects = Projects::where('manager_id', auth()->id())->get();
        $users = User::where('role', '!=', 'manager')->get();

        $selectedProject = $request->get('project_id');

        return view('manager.tasks.create', compact('projects', 'users', 'selectedProject'));
    }

   public function store(Request $request)
{
    $request->validate([
        'project_id' => ['required', Rule::exists('projects', 'id')->where('manager_id', auth()->id())],
        'assigned_to' => ['nullable', 'exists:users,id'],
        'title' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
        'status' => ['nullable', Rule::in(['todo', 'in_progress', 'done'])],
        'due_date' => ['nullable', 'date'],
    ]);

    $task = Tasks::create([
        'project_id' => $request->project_id,
        'assigned_to' => $request->assigned_to,
        'created_by' => auth()->id(),
        'title' => $request->title,
        'description' => $request->description,
        'priority' => $request->priority ?? 'medium',
        'status' => $request->status ?? 'todo',
        'due_date' => $request->due_date,
    ]);

    if ($request->assigned_to) {
        $project = $task->project;
        if ($project) {
            $project->teamMembers()->syncWithoutDetaching([$request->assigned_to]);
        }
    }

    return redirect()->route('manager.tasks.index')
                    ->with('success', 'Task created successfully!');
}

    public function show($id)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->with(['project', 'user', 'assignee', 'subtasks'])
          ->findOrFail($id);

        return view('manager.tasks.show', compact('task'));
    }

     public function edit($id)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->findOrFail($id);

        $projects = Projects::where('manager_id', auth()->id())->get();
        $users = User::where('role', '!=', 'manager')->get();

        return view('manager.tasks.edit', compact('task', 'projects', 'users'));
    }

   public function update(Request $request, $id)
{
    $task = Tasks::whereHas('project', function($query) {
        $query->where('manager_id', auth()->id());
    })->findOrFail($id);

    $request->validate([
        'project_id' => ['sometimes', Rule::exists('projects', 'id')->where('manager_id', auth()->id())],
        'assigned_to' => ['sometimes', 'nullable', 'exists:users,id'],
        'title' => ['sometimes', 'required', 'string', 'max:255'],
        'description' => ['sometimes', 'nullable', 'string'],
        'priority' => ['sometimes', 'nullable', Rule::in(['low', 'medium', 'high'])],
        'status' => ['sometimes', 'nullable', Rule::in(['todo', 'in_progress', 'done'])],
        'due_date' => ['sometimes', 'nullable', 'date'],
    ]);

    // Update task with explicit field assignment
    $task->title = $request->title;
    $task->project_id = $request->project_id;
    $task->assigned_to = $request->assigned_to;
    $task->description = $request->description;
    $task->priority = $request->priority;
    $task->status = $request->status;

    // Handle due_date - ensure proper format
    if ($request->filled('due_date')) {
        $task->due_date = \Carbon\Carbon::parse($request->due_date);
    } else {
        $task->due_date = null;
    }

    $task->save();

    return redirect()->route('manager.tasks.show', $task->id)
                    ->with('success', 'Task updated successfully!');
}
    public function destroy($id)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->findOrFail($id);

        $task->delete();

        return redirect()->route('manager.tasks.index')
                        ->with('success', 'Task deleted successfully!');
    }

    public function markAsComplete($id)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->findOrFail($id);

        $task->update(['status' => 'done']);

        return redirect()->back()
                        ->with('success', 'Task marked as completed!');
    }

    public function markAsInProgress($id)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->findOrFail($id);

        $task->update(['status' => 'in_progress']);

        return redirect()->back()
                        ->with('success', 'Task marked as in progress!');
    }
}
