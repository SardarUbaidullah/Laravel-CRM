<?php

namespace App\Http\Controllers;

use App\Models\Files;
use App\Models\Projects;
use App\Models\Tasks;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class FileController extends Controller
{
    // GET /files
    public function index(Request $request)
    {
        $user = auth()->user();

        // Base query for files
        $query = Files::with(['project','task','user','versions'])
            ->withCount(['versions as child_versions_count'])
            ->latestVersions()
            ->latest();

        // Role-based file access control
        if ($user->role === 'user') {
            // Team Member: Can see files from projects where they have tasks
            $query->whereHas('project.tasks', function($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        } elseif ($user->role === 'admin') {
            // Manager: Can see files from projects they manage
            $query->whereHas('project', function($q) use ($user) {
                $q->where('manager_id', $user->id);
            });
        }
        // Super Admin can see all files (no additional conditions)

        // Apply filters if any (for super admin and managers)
        if ($user->role !== 'user' && $request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($user->role !== 'user' && $request->filled('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($user->role !== 'user' && $request->filled('type')) {
            if ($request->type === 'project') {
                $query->whereNotNull('project_id');
            } elseif ($request->type === 'general') {
                $query->whereNull('project_id');
            }
        }

        $files = $query->get();

        // Calculate stats based on accessible files
        $totalFiles = $files->count();
        $projectFiles = $files->whereNotNull('project_id')->count();
        $generalFiles = $files->whereNull('project_id')->count();
        $totalVersions = Files::whereIn('id', $files->pluck('id'))->count();

        // Get projects and tasks based on user role
        if ($user->role === 'super_admin') {
            $projects = Projects::all();
            $tasks = Tasks::all();
        } elseif ($user->role === 'admin') {
            $projects = Projects::where('manager_id', $user->id)->get();
            $tasks = Tasks::whereIn('project_id', $projects->pluck('id'))->get();
        } else {
            // Team member: get projects where they have tasks and tasks assigned to them
            $projects = Projects::whereHas('tasks', function($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })->get();
            $tasks = Tasks::where('assigned_to', $user->id)->get();
        }

        $users = User::all();

        return view('admin.files.index', compact(
            'files',
            'projects',
            'tasks',
            'users',
            'totalFiles',
            'projectFiles',
            'generalFiles',
            'totalVersions'
        ));
    }

    // GET /files/create
    public function create()
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            $projects = Projects::all();
            $tasks = Tasks::all();
        } elseif ($user->role === 'admin') {
            $projects = Projects::where('manager_id', $user->id)->get();
            $tasks = Tasks::whereIn('project_id', $projects->pluck('id'))->get();
        } else {
            // Team member: get projects where they have tasks and tasks assigned to them
            $projects = Projects::whereHas('tasks', function($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })->get();
            $tasks = Tasks::where('assigned_to', $user->id)->get();
        }

        $users = User::all();

        return view('admin.files.create', compact('projects', 'tasks', 'users'));
    }

    // POST /files
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'project_id' => ['nullable','exists:projects,id'],
            'task_id'    => ['nullable','exists:tasks,id'],
            'user_id'    => ['required','exists:users,id'],
            'file_path'  => ['required','file','max:10240'],
            'file_name'  => ['nullable','string','max:255'],
            'version'    => ['nullable','integer','min:1'],
            'description' => ['nullable','string','max:500'],
        ]);

        // Role-based validation for project/task assignment
        if ($validated['project_id']) {
            $project = Projects::find($validated['project_id']);

            if ($user->role === 'admin' && $project->manager_id !== $user->id) {
                return redirect()->back()->with('error', 'You can only upload files to projects you manage.');
            }

            if ($user->role === 'user') {
                // Team member can only upload to projects where they have tasks
                $hasTaskInProject = Tasks::where('project_id', $validated['project_id'])
                    ->where('assigned_to', $user->id)
                    ->exists();

                if (!$hasTaskInProject) {
                    return redirect()->back()->with('error', 'You can only upload files to projects where you have assigned tasks.');
                }

                // Validate task assignment if task_id is provided
                if ($validated['task_id']) {
                    $task = Tasks::find($validated['task_id']);
                    if ($task->assigned_to !== $user->id) {
                        return redirect()->back()->with('error', 'You can only upload files to tasks assigned to you.');
                    }
                }
            }
        }

        $uploaded = $request->file('file_path');
        $path = $uploaded->store('files', 'public');
        $fileName = $validated['file_name'] ?? $uploaded->getClientOriginalName();
        $version  = $validated['version'] ?? 1;

        $file = Files::create([
            'project_id' => $validated['project_id'] ?? null,
            'task_id'    => $validated['task_id'] ?? null,
            'user_id'    => $validated['user_id'],
            'file_name'  => $fileName,
            'file_path'  => $path,
            'file_size'  => $uploaded->getSize(),
            'mime_type'  => $uploaded->getMimeType(),
            'version'    => $version,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('files.index')->with('success', 'File uploaded successfully!');
    }

    // GET /files/{id}
    public function show($id)
    {
        $file = Files::with(['project','task','user','versions.user'])->findOrFail($id);

        // Check access
        if (!$this->canAccessFile($file)) {
            return redirect()->route('files.index')->with('error', 'You do not have permission to access this file.');
        }

        return view('admin.files.show', compact('file'));
    }

    // GET /files/{id}/edit
    public function edit($id)
    {
        $file = Files::findOrFail($id);

        // Check access
        if (!$this->canAccessFile($file)) {
            return redirect()->route('files.index')->with('error', 'You do not have permission to edit this file.');
        }

        $user = auth()->user();

        if ($user->role === 'super_admin') {
            $projects = Projects::all();
            $tasks = Tasks::all();
        } elseif ($user->role === 'admin') {
            $projects = Projects::where('manager_id', $user->id)->get();
            $tasks = Tasks::whereIn('project_id', $projects->pluck('id'))->get();
        } else {
            // Team member: get projects where they have tasks and tasks assigned to them
            $projects = Projects::whereHas('tasks', function($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })->get();
            $tasks = Tasks::where('assigned_to', $user->id)->get();
        }

        $users = User::all();

        return view('admin.files.edit', compact('file', 'projects', 'tasks', 'users'));
    }

    // PUT/PATCH /files/{id}
    public function update(Request $request, $id)
    {
        $file = Files::findOrFail($id);

        // Check access
        if (!$this->canAccessFile($file)) {
            return redirect()->route('files.index')->with('error', 'You do not have permission to update this file.');
        }

        $validated = $request->validate([
            'project_id' => ['nullable','exists:projects,id'],
            'task_id'    => ['nullable','exists:tasks,id'],
            'user_id'    => ['required','exists:users,id'],
            'file_name'  => ['required','string','max:255'],
            'version'    => ['required','integer','min:1'],
            'description' => ['nullable','string','max:500'],
        ]);

        $file->update($validated);

        return redirect()->route('files.show', $file->id)->with('success', 'File updated successfully!');
    }

    // DELETE /files/{id}
    public function destroy($id)
    {
        $file = Files::findOrFail($id);

        // Check access
        if (!$this->canAccessFile($file)) {
            return redirect()->route('files.index')->with('error', 'You do not have permission to delete this file.');
        }

        // Delete all versions first
        foreach ($file->versions as $version) {
            if ($version->file_path && Storage::disk('public')->exists($version->file_path)) {
                Storage::disk('public')->delete($version->file_path);
            }
            $version->delete();
        }

        // Delete main file
        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        return redirect()->route('files.index')->with('success', 'File and all versions deleted successfully!');
    }

    // Download file
    public function download($id)
    {
        $file = Files::findOrFail($id);

        if (!Storage::disk('public')->exists($file->file_path)) {
            return redirect()->route('files.index')->with('error', 'File not found on server.');
        }

        // Get the correct file path and set proper headers
        $filePath = Storage::disk('public')->path($file->file_path);

        return response()->download($filePath, $file->file_name, [
            'Content-Type' => $file->mime_type,
            'Content-Disposition' => 'attachment; filename="' . $file->file_name . '"',
        ]);
    }

    // Preview file
    public function preview($id)
    {
        $file = Files::findOrFail($id);

        if (!Storage::disk('public')->exists($file->file_path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // For images and PDFs, return the file for preview
        if ($file->is_image || $file->is_pdf) {
            $fileContent = Storage::disk('public')->get($file->file_path);
            $response = response($fileContent, 200);
            $response->header('Content-Type', $file->mime_type);
            $response->header('Content-Disposition', 'inline; filename="' . $file->file_name . '"');
            return $response;
        }

        // For other file types, offer download
        return $this->download($id);
    }

    // GET /files/{id}/new-version - Show new version form
    public function showNewVersionForm($id)
    {
        $file = Files::with(['project','task','user'])->findOrFail($id);

        // Check access
        if (!$this->canAccessFile($file)) {
            return redirect()->route('files.index')->with('error', 'You do not have permission to upload new versions for this file.');
        }

        return view('admin.files.new-version', compact('file'));
    }

    // POST /files/{id}/new-version - Store new version
    public function newVersion(Request $request, $id)
    {
        $parentFile = Files::findOrFail($id);

        // Check access
        if (!$this->canAccessFile($parentFile)) {
            return redirect()->route('files.index')->with('error', 'You do not have permission to upload new versions for this file.');
        }

        $validated = $request->validate([
            'file_path' => ['required','file','max:10240'],
            'description' => ['nullable','string','max:500'],
        ]);

        $uploaded = $request->file('file_path');
        $path = $uploaded->store('files', 'public');

        // Get next version number
        $maxVersion = Files::where('id', $parentFile->id)
            ->orWhere('parent_id', $parentFile->id)
            ->max('version');

        $newVersion = $maxVersion + 1;

        $file = Files::create([
            'project_id' => $parentFile->project_id,
            'task_id'    => $parentFile->task_id,
            'user_id'    => auth()->id(),
            'file_name'  => $uploaded->getClientOriginalName(),
            'file_path'  => $path,
            'file_size'  => $uploaded->getSize(),
            'mime_type'  => $uploaded->getMimeType(),
            'version'    => $newVersion,
            'parent_id'  => $parentFile->id,
            'description' => $validated['description'] ?? "Version {$newVersion} - " . ($uploaded->getClientOriginalName() !== $parentFile->file_name ? "File renamed to: " . $uploaded->getClientOriginalName() : "Updated file"),
        ]);

        return redirect()->route('files.show', $parentFile->id)->with('success', "New version {$newVersion} uploaded successfully!");
    }

    /**
     * Check if current user can access the file
     */
    private function canAccessFile($file)
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->role === 'admin') {
            // Manager can access files from projects they manage
            return $file->project && $file->project->manager_id === $user->id;
        }

        if ($user->role === 'user') {
            // Team member can access files from projects where they have tasks
            if (!$file->project) {
                return false; // Team members can't access general files
            }

            return Tasks::where('project_id', $file->project_id)
                ->where('assigned_to', $user->id)
                ->exists();
        }

        return false;
    }
}
