<?php

namespace App\Http\Controllers;

use App\Models\teams;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    // Show all teams
    public function index()
    {
        $teams = teams::all();
        return view('admin.teams.index', compact('teams'));
    }

    // Show create form
    public function create()
    {
        return view('admin.teams.create');
    }

    // Store a new team
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_id' => 'required|exists:users,id'
        ]);

        teams::create($request->all());

        return redirect()->route('admin.teams.index')->with('success', 'Team created successfully.');
    }

    // Show single team
    public function show($id)
    {
        $team = teams::findOrFail($id);
        return view('admin.teams.show', compact('team'));
    }

    // Show edit form
    public function edit($id)
    {
        $team = teams::findOrFail($id);
        return view('admin.teams.edit', compact('team'));
    }

    // Update team
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_id' => 'required|exists:users,id'
        ]);

        $team = teams::findOrFail($id);
        $team->update($request->all());

        return redirect()->route('admin.teams.index')->with('success', 'Team updated successfully.');
    }

    // Delete team
    public function destroy($id)
    {
        $team = teams::findOrFail($id);
        $team->delete();

        return redirect()->route('admin.teams.index')->with('success', 'Team deleted successfully.');
    }
}
