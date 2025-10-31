<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use App\Models\Projects;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    // GET /tasks
    public function index()
    {
        $tasks = Tasks::with(['project','assignee','creator'])->get();
        return view('admin.tasks.index', compact('tasks'));
    }

    // GET /tasks/create
    public function create()
    {
        $projects = Projects::all();
        $users = User::all();
        return view('admin.tasks.create', compact('projects', 'users'));
    }

    // POST /tasks
    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'  => ['required','exists:projects,id'],
            'assigned_to' => ['nullable','exists:users,id'],
            'created_by'  => ['nullable','exists:users,id'],
            'title'       => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'priority'    => ['nullable', Rule::in(['low','medium','high'])],
            'status'      => ['nullable', Rule::in(['todo','in_progress','done'])],
            'start_date'  => ['nullable','date'],
            'due_date'    => ['nullable','date'],
        ]);

        Tasks::create($data);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully');
    }

    // GET /tasks/{id}
    public function show($id)
    {
        $task = Tasks::with(['project','assignee','creator'])->find($id);

        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Task not found');
        }

        return view('admin.tasks.show', compact('task'));
    }

    // GET /tasks/{id}/edit
    public function edit($id)
    {
        $task = Tasks::find($id);
        $projects = Projects::all();
        $users = User::all();

        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Task not found');
        }

        return view('admin.tasks.edit', compact('task', 'projects', 'users'));
    }

    // PUT /tasks/{id}
    public function update(Request $request, $id)
    {
        $task = Tasks::find($id);

        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Task not found');
        }

        $data = $request->validate([
            'project_id'  => ['sometimes','required','exists:projects,id'],
            'assigned_to' => ['sometimes','nullable','exists:users,id'],
            'created_by'  => ['sometimes','nullable','exists:users,id'],
            'title'       => ['sometimes','required','string','max:255'],
            'description' => ['sometimes','nullable','string'],
            'priority'    => ['sometimes','nullable', Rule::in(['low','medium','high'])],
            'status'      => ['sometimes','nullable', Rule::in(['todo','in_progress','done'])],
            'start_date'  => ['sometimes','nullable','date'],
            'due_date'    => ['sometimes','nullable','date'],
        ]);

        $task->update($data);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully');
    }

    // DELETE /tasks/{id}
    public function destroy($id)
    {
        $task = Tasks::find($id);

        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Task not found');
        }

        $task->delete(); // soft delete
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully');
    }
}
