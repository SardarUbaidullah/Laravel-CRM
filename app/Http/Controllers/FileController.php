<?php

namespace App\Http\Controllers;

use App\Models\Files;
use App\Models\Projects;
use App\Models\Tasks;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FileController extends Controller
{
    // GET /files
    public function index(Request $request)
    {
        $query = Files::with(['project','task','user'])->latest();

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->filled('task_id')) {
            $query->where('task_id', $request->task_id);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $files = $query->get();
        $projects = Projects::all();
        $tasks = Tasks::all();
        $users = User::all();

        return view('admin.files.index', compact('files', 'projects', 'tasks', 'users'));
    }

    // GET /files/create
    public function create()
    {
        $projects = Projects::all();
        $tasks = Tasks::all();
        $users = User::all();
        return view('admin.files.create', compact('projects', 'tasks', 'users'));
    }

    // POST /files
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['nullable','exists:projects,id'],
            'task_id'    => ['nullable','exists:tasks,id'],
            'user_id'    => ['required','exists:users,id'],
            'file_path'  => ['required','file','max:10240'],
            'file_name'  => ['nullable','string','max:255'],
            'version'    => ['nullable','integer','min:1'],
        ]);

        $uploaded = $request->file('file_path');
        $path = $uploaded->store('files', 'public');
        $fileName = $validated['file_name'] ?? $uploaded->getClientOriginalName();
        $version  = $validated['version'] ?? 1;

        Files::create([
            'project_id' => $validated['project_id'] ?? null,
            'task_id'    => $validated['task_id'] ?? null,
            'user_id'    => $validated['user_id'],
            'file_name'  => $fileName,
            'file_path'  => $path,
            'version'    => $version,
        ]);

        return redirect()->route('files.index')->with('success', 'File uploaded successfully!');
    }

    // GET /files/{id}
    public function show($id)
    {
        $file = Files::with(['project','task','user'])->find($id);

        if (!$file) {
            return redirect()->route('files.index')->with('error', 'File not found');
        }

        return view('admin.files.show', compact('file'));
    }

    // GET /files/{id}/edit
    public function edit($id)
    {
        $file = Files::find($id);
        $projects = Projects::all();
        $tasks = Tasks::all();
        $users = User::all();

        if (!$file) {
            return redirect()->route('files.index')->with('error', 'File not found');
        }

        return view('admin.files.edit', compact('file', 'projects', 'tasks', 'users'));
    }

    // PUT/PATCH /files/{id}
    public function update(Request $request, $id)
    {
        $file = Files::find($id);
        if (!$file) {
            return redirect()->route('files.index')->with('error', 'File not found');
        }

        $validated = $request->validate([
            'project_id' => ['nullable','exists:projects,id'],
            'task_id'    => ['nullable','exists:tasks,id'],
            'user_id'    => ['nullable','exists:users,id'],
            'file_path'  => ['sometimes','file','max:10240'],
            'file_name'  => ['nullable','string','max:255'],
            'version'    => ['nullable','integer','min:1'],
        ]);

        if ($request->hasFile('file_path')) {
            // Delete old file
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            $uploaded = $request->file('file_path');
            $path = $uploaded->store('files', 'public');
            $file->file_path = $path;

            if (!isset($validated['file_name'])) {
                $file->file_name = $uploaded->getClientOriginalName();
            }
        }

        $file->project_id = $validated['project_id'] ?? $file->project_id;
        $file->task_id = $validated['task_id'] ?? $file->task_id;
        $file->user_id = $validated['user_id'] ?? $file->user_id;
        $file->file_name = $validated['file_name'] ?? $file->file_name;
        $file->version = $validated['version'] ?? $file->version;

        $file->save();

        return redirect()->route('files.index')->with('success', 'File updated successfully!');
    }

    // DELETE /files/{id}
    public function destroy($id)
    {
        $file = Files::find($id);
        if (!$file) {
            return redirect()->route('files.index')->with('error', 'File not found');
        }

        // Delete physical file
        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        return redirect()->route('files.index')->with('success', 'File deleted successfully!');
    }

    // Download file
    public function download($id)
    {
        $file = Files::find($id);

        if (!$file || !Storage::disk('public')->exists($file->file_path)) {
            return redirect()->route('files.index')->with('error', 'File not found');
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }
}
