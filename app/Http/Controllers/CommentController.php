<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Projects;
use App\Models\Tasks;
use App\Models\Files;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // ==================== STORE METHODS ====================

    public function storeProjectComment(Request $request, Projects $project)
    {
        // Use 'create' instead of 'comment' or check if your policy has 'comment' method
        $this->authorize('create', [Comment::class, $project]);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'is_internal' => 'sometimes|boolean'
        ]);

        $comment = Comment::create([
            'content' => $validated['content'],
            'user_id' => Auth::id(),
            'commentable_type' => Projects::class,
            'commentable_id' => $project->id,
            'is_internal' => $this->determineInternalStatus($validated['is_internal'] ?? null)
        ]);

        return $this->redirectBackWithSuccess('Comment added successfully');
    }

    public function storeTaskComment(Request $request, Tasks $task)
    {
        $this->authorize('create', [Comment::class, $task]);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'is_internal' => 'sometimes|boolean'
        ]);

        $comment = Comment::create([
            'content' => $validated['content'],
            'user_id' => Auth::id(),
            'commentable_type' => Tasks::class,
            'commentable_id' => $task->id,
            'is_internal' => $this->determineInternalStatus($validated['is_internal'] ?? null)
        ]);

        return $this->redirectBackWithSuccess('Comment added successfully');
    }

    public function storeFileComment(Request $request, Files $file)
    {
        $this->authorize('create', [Comment::class, $file]);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'is_internal' => 'sometimes|boolean'
        ]);

        $comment = Comment::create([
            'content' => $validated['content'],
            'user_id' => Auth::id(),
            'commentable_type' => Files::class,
            'commentable_id' => $file->id,
            'is_internal' => $this->determineInternalStatus($validated['is_internal'] ?? null)
        ]);

        return $this->redirectBackWithSuccess('Comment added successfully');
    }

    // ==================== UPDATE & DELETE ====================

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $comment->update(['content' => $validated['content']]);

        return $this->redirectBackWithSuccess('Comment updated successfully');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return $this->redirectBackWithSuccess('Comment deleted successfully');
    }

    // ==================== HELPER METHODS ====================

    private function determineInternalStatus($requestedStatus)
    {
        $user = Auth::user();

        // Clients can only post public comments
        if ($user->role === 'client') {
            return false;
        }

        // For team members, use requested status or default to public for safety
        return $requestedStatus ?? false;
    }

    private function redirectBackWithSuccess($message)
    {
        return redirect()->back()->with('success', $message);
    }

    // ==================== API METHODS (Optional) ====================

    public function getComments(Request $request)
    {
        $request->validate([
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|integer'
        ]);

        $commentable = $this->getCommentableModel(
            $request->commentable_type,
            $request->commentable_id
        );

        if (!$commentable) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        // Check if user can view the commentable resource
        $this->authorize('view', $commentable);

        $user = Auth::user();
        $comments = $commentable->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($comment) use ($user) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'role' => $comment->user->role,
                        'avatar' => $this->getUserAvatar($comment->user) // Fixed method call
                    ],
                    'is_internal' => $comment->is_internal,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'can_edit' => $user->can('update', $comment), // Use policy instead of model method
                    'can_delete' => $user->can('delete', $comment) // Use policy instead of model method
                ];
            });

        return response()->json(['comments' => $comments]);
    }

    private function getCommentableModel($type, $id)
    {
        switch ($type) {
            case 'project':
                return Projects::find($id);
            case 'task':
                return Tasks::find($id);
            case 'file':
                return Files::find($id);
            default:
                return null;
        }
    }

    private function getUserAvatar($user)
    {
        // Simple avatar fallback - you can customize this
        return 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF';
    }
}
