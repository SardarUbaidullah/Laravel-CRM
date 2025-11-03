<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Projects;
use App\Models\Tasks;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'manager' && auth()->user()->role !== 'admin') {
                abort(403, 'Unauthorized access.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        // Get team members working on manager's projects
        $teamMembers = User::whereHas('assignedTasks', function ($q) {
            $q->whereHas('project', function($query) {
                $query->where('manager_id', auth()->id());
            });
        })->withCount(['assignedTasks as pending_tasks_count' => function($query) {
            $query->whereHas('project', function($q) {
                $q->where('manager_id', auth()->id());
            })->where('status', '!=', 'done');
        }, 'assignedTasks as total_tasks_count' => function($query) {
            $query->whereHas('project', function($q) {
                $q->where('manager_id', auth()->id());
            });
        }])->get();

        return view('manager.team.index', compact('teamMembers'));
    }

    public function show($id)
    {
        $teamMember = User::whereHas('assignedTasks', function ($q) {
            $q->whereHas('project', function($query) {
                $query->where('manager_id', auth()->id());
            });
        })->with(['assignedTasks' => function($query) {
            $query->whereHas('project', function($q) {
                $q->where('manager_id', auth()->id());
            })->with('project');
        }])->findOrFail($id);

        return view('manager.team.show', compact('teamMember'));
    }
}
