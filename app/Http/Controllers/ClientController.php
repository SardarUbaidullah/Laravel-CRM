<?php
// app/Http/Controllers/ClientController.php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Models\Tasks;
use App\Models\Files;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function dashboard()
    {
        $client = auth()->user();

        $projects = Projects::where('client_id', $client->client_id)
            ->withCount(['tasks', 'completedTasks'])
            ->with(['manager'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentActivities = $this->getRecentActivities($client);
        $upcomingDeadlines = $this->getUpcomingDeadlines($client);

        return view('client.dashboard', compact('projects', 'recentActivities', 'upcomingDeadlines'));
    }

    public function projects()
    {
        $client = auth()->user();
        $projects = Projects::where('client_id', $client->client_id)
            ->with(['manager'])
            ->withCount(['tasks', 'completedTasks'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client.projects.index', compact('projects'));
    }

    public function projectShow(Projects $project)
    {
        $client = auth()->user();

        if ($project->client_id !== $client->client_id) {
            abort(403, 'Access denied');
        }

        $project->load([
            'manager',
            'tasks' => function($query) {
                $query->with('assignedTo')->orderBy('due_date', 'asc');
            },
            'files' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'comments' => function($query) {
                $query->with('user')->orderBy('created_at', 'desc');
            }
        ]);

        return view('client.projects.show', compact('project'));
    }

    public function addProjectComment(Request $request, Projects $project)
    {
        $client = auth()->user();

        if ($project->client_id !== $client->client_id) {
            abort(403, 'Access denied');
        }

        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        Comment::create([
            'content' => $request->content,
            'user_id' => $client->id,
            'commentable_type' => Projects::class,
            'commentable_id' => $project->id,
            'is_internal' => false
        ]);

        return redirect()->back()->with('success', 'Comment added successfully');
    }

    public function addTaskComment(Request $request, Tasks $task)
    {
        $client = auth()->user();

        if ($task->project->client_id !== $client->client_id) {
            abort(403, 'Access denied');
        }

        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        Comment::create([
            'content' => $request->content,
            'user_id' => $client->id,
            'commentable_type' => Tasks::class,
            'commentable_id' => $task->id,
            'is_internal' => false
        ]);

        return redirect()->back()->with('success', 'Comment added successfully');
    }

    public function downloadFile(Files $file)
    {
        $client = auth()->user();

        if ($file->project->client_id !== $client->client_id) {
            abort(403, 'Access denied');
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($file->file_path, $file->original_name);
    }

    private function getRecentActivities($client)
    {
        $projectComments = Comment::whereHasMorph(
            'commentable',
            [Projects::class],
            function($query) use ($client) {
                $query->where('client_id', $client->client_id);
            }
        )->with(['user', 'commentable'])->latest()->limit(5)->get();

        $taskComments = Comment::whereHasMorph(
            'commentable',
            [Tasks::class],
            function($query) use ($client) {
                $query->whereHas('project', function($q) use ($client) {
                    $q->where('client_id', $client->client_id);
                });
            }
        )->with(['user', 'commentable.project'])->latest()->limit(5)->get();

        return $projectComments->merge($taskComments)->sortByDesc('created_at')->take(5);
    }

    private function getUpcomingDeadlines($client)
    {
        return Tasks::whereHas('project', function($query) use ($client) {
            $query->where('client_id', $client->client_id);
        })
        ->where('due_date', '>=', now())
        ->whereIn('status', ['todo', 'in_progress'])
        ->orderBy('due_date')
        ->limit(5)
        ->get();
    }
}
