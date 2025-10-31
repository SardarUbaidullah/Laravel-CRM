<?php

namespace App\Http\Controllers;

use App\Models\time_logs;
use App\Models\Tasks;
use App\Models\User;
use Illuminate\Http\Request;

class TimeLogController extends Controller
{
    public function index()
    {
        $timeLogs = time_logs::with(['task', 'user'])->latest()->get();
        $tasks = Tasks::all();
        $users = User::all();

        return view('admin.time-logs.index', compact('timeLogs', 'tasks', 'users'));
    }

    public function create()
    {
        $tasks = Tasks::all();
        $users = User::all();
        return view('admin.time-logs.create', compact('tasks', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'user_id' => 'required|exists:users,id',
            'hours' => 'required|numeric|min:0.1',
            'log_date' => 'required|date',
        ]);

        time_logs::create($request->all());

        return redirect()->route('time-logs.index')->with('success', 'Time log created successfully!');
    }

    public function show($id)
    {
        $timeLog = time_logs::with(['task', 'user'])->findOrFail($id);
        return view('admin.time-logs.show', compact('timeLog'));
    }

    public function edit($id)
    {
        $timeLog = time_logs::findOrFail($id);
        $tasks = Tasks::all();
        $users = User::all();

        return view('admin.time-logs.edit', compact('timeLog', 'tasks', 'users'));
    }

    public function update(Request $request, $id)
    {
        $timeLog = time_logs::findOrFail($id);

        $request->validate([
            'task_id' => 'exists:tasks,id',
            'user_id' => 'exists:users,id',
            'hours' => 'numeric|min:0.1',
            'log_date' => 'date',
        ]);

        $timeLog->update($request->all());

        return redirect()->route('time-logs.index')->with('success', 'Time log updated successfully!');
    }

    public function destroy($id)
    {
        $timeLog = time_logs::findOrFail($id);
        $timeLog->delete();

        return redirect()->route('time-logs.index')->with('success', 'Time log deleted successfully!');
    }
}
