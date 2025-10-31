<?php

namespace App\Http\Controllers;

use App\Models\Milestones;
use App\Models\Projects;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('project_id')) {
            $milestones = Milestones::where('project_id', $request->project_id)->get();
            $project = Projects::find($request->project_id);
        } else {
            $milestones = Milestones::all();
            $project = null;
        }

        $projects = Projects::all();
        return view('admin.milestones.index', compact('milestones', 'projects', 'project'));
    }

    public function create()
    {
        $projects = Projects::all();
        return view('admin.milestones.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'status' => 'in:pending,in-progress,completed'
        ]);

        Milestones::create($request->all());

        return redirect()->route('milestones.index')->with('success', 'Milestone created successfully!');
    }

    public function show($id)
    {
        $milestone = Milestones::findOrFail($id);
        return view('admin.milestones.show', compact('milestone'));
    }

    public function edit($id)
    {
        $milestone = Milestones::findOrFail($id);
        $projects = Projects::all();
        return view('admin.milestones.edit', compact('milestone', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $milestone = Milestones::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'due_date' => 'nullable|date',
            'status' => 'in:pending,in-progress,completed'
        ]);

        $milestone->update($request->all());

        return redirect()->route('milestones.index')->with('success', 'Milestone updated successfully!');
    }

    public function destroy($id)
    {
        $milestone = Milestones::findOrFail($id);
        $milestone->delete();

        return redirect()->route('milestones.index')->with('success', 'Milestone deleted successfully!');
    }
}
