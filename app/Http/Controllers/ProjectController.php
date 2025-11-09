<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // GET /projects
    public function index()
    {
        $projects = Projects::all();
        return view('projects.index', compact('projects'));
    }

    // GET /projects/create
  public function create()
{
    $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
    $clients = Client::active()->get(); // Get active clients
    $teamMembers = User::where('role', 'user')->get(); // Get regular users for team members

    return view('projects.create', compact('managers', 'clients', 'teamMembers'));
}

// POST /projects
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'client_id' => 'required|exists:clients,id',
    ]);

    $project = Projects::create([
        'team_id' => $request->team_id,
        'name' => $request->name,
        'description' => $request->description,
        'start_date' => $request->start_date,
        'due_date' => $request->due_date,
        'manager_id' => $request->manager_id,
        'client_id' => $request->client_id, // Add client_id
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
        $project = Projects::find($id);

        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Project not found');
        }

        return view('projects.show', compact('project'));
    }

    // GET /projects/{id}/edit
    public function edit($id)
    {
        $project = Projects::find($id);

        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Project not found');
        }

        return view('projects.edit', compact('project'));
    }

    // PUT /projects/{id}
    public function update(Request $request, $id)
    {
        $project = Projects::find($id);

        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Project not found');
        }

        $project->update([
            'team_id' => $request->team_id,
            'name' => $request->name ?? $project->name,
            'description' => $request->description ?? $project->description,
            'start_date' => $request->start_date ?? $project->start_date,
            'due_date' => $request->due_date ?? $project->due_date,
            'status' => $request->status ?? $project->status,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully');
    }

    // DELETE /projects/{id}
    public function destroy($id)
    {
        $project = Projects::find($id);

        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Project not found');
        }

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully');
    }
}
