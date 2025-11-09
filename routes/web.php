<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\TimeReportController;
use App\Http\Controllers\Admin\TimeTrackingController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\TeamOwnController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TeamChatController;
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
// Calendar Routes




 Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Manager routes
Route::prefix('chat')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('manager.chat.index');
    Route::get('/project/{project}', [ChatController::class, 'projectChat'])->name('manager.chat.project');
    Route::get('/user/{user}', [ChatController::class, 'directChat'])->name('manager.chat.direct');
    Route::post('/{chatRoom}/send', [ChatController::class, 'sendMessage'])->name('manager.chat.send');
    Route::post('/{chatRoom}/read', [ChatController::class, 'markAsRead'])->name('manager.chat.read');
    Route::get('/{chatRoom}/messages', [ChatController::class, 'getMessages'])->name('manager.chat.messages');
});
Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

// Team routes
Route::prefix('team')->name('team.')->group(function () {
    Route::get('/', [TeamOwnController::class, 'index'])->name('index');
    Route::get('/tasks', [TeamOwnController::class, 'tasks'])->name('tasks.index');
    Route::get('/tasks/{id}', [TeamOwnController::class, 'showTask'])->name('tasks.show');
     Route::post('/tasks/{task}/complete-task', [TeamOwnController::class, 'completeTask'])->name('tasks.complete-task');
    Route::post('/tasks/{task}/update-status', [TeamOwnController::class, 'updateStatus'])->name('tasks.update-status');
    Route::get('/projects', [TeamOwnController::class, 'projects'])->name('projects');
    Route::get('/profile', [TeamOwnController::class, 'profile'])->name('profile');
    Route::post('/tasks/{task}/complete', [TeamOwnController::class, 'complete'])->name('tasks.complete');

    // Team chat routes - using same controller but different route names
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/project/{project}', [ChatController::class, 'projectChat'])->name('project');
        Route::get('/user/{user}', [ChatController::class, 'directChat'])->name('direct');
        Route::post('/{chatRoom}/send', [ChatController::class, 'sendMessage'])->name('send');
        Route::post('/{chatRoom}/read', [ChatController::class, 'markAsRead'])->name('read');
        Route::get('/{chatRoom}/messages', [ChatController::class, 'getMessages'])->name('messages');
    });
});
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

    Route::get('/calendar', [App\Http\Controllers\Manager\CalendarController::class, 'index'])->name('manager.calendar.index');
Route::get('/calendar/events', [App\Http\Controllers\Manager\CalendarController::class, 'getEvents'])->name('manager.calendar.events');


 Route::resource('files', FileController::class);
    Route::get('/files/{id}/download', [FileController::class, 'download'])->name('files.download');
    Route::get('/files/{id}/preview', [FileController::class, 'preview'])->name('files.preview');

     Route::get('/files/{id}/new-version', [FileController::class, 'showNewVersionForm'])->name('files.new-version-form');
    Route::post('/files/{id}/new-version', [FileController::class, 'newVersion'])->name('files.new-version');
    // Admin - Users
    Route::resource('users', UserController::class);


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





});

// Reports Routes for Super Admin
   // routes/web.php
Route::prefix('admin')->middleware(['auth', 'super_admin'])->group(function () {
    Route::prefix('reports')->group(function () {
        Route::get('/', [AdminReportController::class, 'index'])->name('admin.reports');
        Route::get('/quick-stats', [AdminReportController::class, 'quickStats'])->name('admin.reports.quick-stats');
        Route::get('/data/{type}', [AdminReportController::class, 'getReportData'])->name('admin.reports.data');
    });
});
// routes/web.php
Route::get('/admin/reports/test', function() {
    return response()->json([
        'message' => 'Reports API is working',
        'total_projects' => \App\Models\Projects::count(),
        'total_tasks' => \App\Models\Tasks::count(),
        'total_users' => \App\Models\User::count()
    ]);
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
        Route::patch('/projects/{project}/status', [ManagerProjectController::class, 'updateStatus'])
    ->name('projects.updateStatus');
// Route::post('/projects/{project}/update-status', [ManagerProjectController::class, 'updateStatus'])->name('projects.update-status');
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







// routes/web.php

Route::prefix('client')->name('client.')->middleware(['auth', 'client.access'])->group(function () {
    Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
    Route::get('/projects', [ClientController::class, 'projects'])->name('projects');
    Route::get('/projects/{project}', [ClientController::class, 'projectShow'])->name('projects.show');

    // Comments
    Route::post('/projects/{project}/comments', [ClientController::class, 'addProjectComment'])->name('projects.comments.store');
    Route::post('/tasks/{task}/comments', [ClientController::class, 'addTaskComment'])->name('tasks.comments.store');

    // Files
    Route::get('/files/{file}/download', [ClientController::class, 'downloadFile'])->name('files.download');
});


Route::prefix('admin')->middleware(['auth'])->group(function () {

    // Professional Time Analytics
    Route::prefix('time-reports')->group(function () {
        Route::get('/', [TimeReportController::class, 'index'])->name('admin.time-reports');
        Route::get('/summary', [TimeReportController::class, 'getTimeSummary']);
        Route::get('/detailed', [TimeReportController::class, 'getDetailedReport']);
        Route::get('/team-activity', [TimeReportController::class, 'getTeamActivity']);
        Route::get('/weekly-summary', [TimeReportController::class, 'getWeeklySummary']);
        Route::post('/export', [TimeReportController::class, 'exportReport']);
    });

   Route::post('/time-tracking/start-timer', [TimeTrackingController::class, 'startTimer']);
        Route::post('/time-tracking/stop-timer', [TimeTrackingController::class, 'stopTimer']);
        Route::get('/time-tracking/running-timer', [TimeTrackingController::class, 'getRunningTimer']);
        Route::get('/time-tracking//task-logs/{taskId}', [TimeTrackingController::class, 'getTaskTimeLogs']);
        Route::post('/time-tracking//manual-entry', [TimeTrackingController::class, 'manualEntry']);
        Route::delete('/time-tracking//delete-log/{id}', [TimeTrackingController::class, 'deleteTimeLog']);
    // Existing Reports
    Route::prefix('reports')->group(function () {
        Route::get('/', [AdminReportController::class, 'index'])->name('admin.reports');
        Route::get('/quick-stats', [AdminReportController::class, 'quickStats']);
        Route::get('/data/{type}', [AdminReportController::class, 'getReportData']);
    });
});

require __DIR__ . '/auth.php';
