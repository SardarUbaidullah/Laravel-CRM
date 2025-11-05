<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Manager\ChatController;
use App\Http\Controllers\SubTaskController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\Projects as Project;


// Manager Controllers
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\ProjectController as ManagerProjectController;
use App\Http\Controllers\Manager\TaskController as ManagerTaskController;
use App\Http\Controllers\Manager\TeamController as manager_TeamController;
use App\Http\Controllers\Manager\SubTaskController as ManagerSubTaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home

 Route::prefix('chat')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('manager.chat.index');
    Route::get('/project/{project}', [ChatController::class, 'projectChat'])->name('manager.chat.project');
    Route::get('/user/{user}', [ChatController::class, 'directChat'])->name('manager.chat.direct');
    Route::post('/{chatRoom}/send', [ChatController::class, 'sendMessage'])->name('manager.chat.send');
    Route::post('/{chatRoom}/read', [ChatController::class, 'markAsRead'])->name('manager.chat.read');
    Route::get('/{chatRoom}/messages', [ChatController::class, 'getMessages'])->name('manager.chat.messages');
});

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Pusher Authentication Route
Route::post('/pusher/auth', function (Request $request) {
    $user = auth()->user();

    if (!$user) {
        return response('Unauthorized', 401);
    }

    $channelName = $request->channel_name;
    $socketId = $request->socket_id;

    // Check if user can access this channel
    if (str_starts_with($channelName, 'private-chat.room.')) {
        $roomId = str_replace('private-chat.room.', '', $channelName);

        // Check if user has access to this chat room
        $hasAccess = \App\Models\ChatRoom::where('id', $roomId)
            ->whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->exists();

        if (!$hasAccess) {
            return response('Forbidden', 403);
        }
    }

    // Generate auth response
    $pusher = new Pusher\Pusher(
        config('broadcasting.connections.pusher.key'),
        config('broadcasting.connections.pusher.secret'),
        config('broadcasting.connections.pusher.app_id'),
        config('broadcasting.connections.pusher.options')
    );

    return $pusher->authorizeChannel($channelName, $socketId);
})->middleware('auth')->name('pusher.auth');
/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function(){
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Route::middleware(['auth', 'super_admin'])->group(function () {

    // Profile

    // Projects
    Route::resource('projects', ProjectController::class);

    // Teams
    Route::resource('teams', TeamController::class);

    // Tasks
    Route::resource('tasks', TaskController::class);

    // Subtasks
    Route::resource('subtasks', SubTaskController::class);

    // Milestones
    Route::resource('milestones', MilestoneController::class);

    // Time Logs
    Route::resource('time-logs', TimeLogController::class);

    // Files
    Route::resource('files', FileController::class);
    Route::get('/files/{id}/download', [FileController::class, 'download'])->name('files.download');

    // Admin - Users
    Route::resource('users', UserController::class);
});

/*
|--------------------------------------------------------------------------
| Manager Routes
|--------------------------------------------------------------------------
*/



Route::prefix('manager')
    ->name('manager.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        // Dashboard

        // Projects
        // Manager Project Routes
Route::get('/projects/running', [ManagerProjectController::class, 'running'])->name('projects.running');
Route::get('/projects/completed', [ManagerProjectController::class, 'completed'])->name('projects.completed');
Route::post('/projects/{id}/complete', [ManagerProjectController::class, 'markComplete'])->name('projects.complete');
Route::post('/projects/{id}/progress', [ManagerProjectController::class, 'markInProgress'])->name('projects.progress');
        Route::resource('projects', ManagerProjectController::class);

        // Tasks
        Route::get('/tasks/pending', [ManagerTaskController::class, 'pendingTasks'])->name('tasks.pending');
Route::get('/tasks/completed', [ManagerTaskController::class, 'completedTasks'])->name('tasks.completed');
Route::post('/tasks/{id}/complete', [ManagerTaskController::class, 'markAsComplete'])->name('tasks.complete');
Route::post('/tasks/{id}/progress', [ManagerTaskController::class, 'markAsInProgress'])->name('tasks.progress');
        // Teams
        Route::resource('tasks', ManagerTaskController::class);

        // Teams
Route::get('/team', [manager_TeamController::class, 'index'])->name('team.index');
 Route::get('/team/{id}', [manager_TeamController::class, 'show'])->name('team.show');
  Route::get('/project/{project}/team', [manager_TeamController::class, 'projectTeam'])
        ->name('project.team');
        // Subtasks (linked to tasks)
        Route::prefix('tasks/{taskId}')->group(function () {
            Route::resource('subtasks', ManagerSubTaskController::class);
        });
    });



require __DIR__ . '/auth.php';
