<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Providers\NotificationService;

use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{


    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }


     public function toggleProjectPermission(Request $request, User $user)
    {
        // Only allow for managers
        if ($user->role !== 'admin') {
            return redirect()->back()->with('error', 'Project creation permission can only be granted to managers.');
        }

        $newPermission = !$user->can_create_project;
        $user->update(['can_create_project' => $newPermission]);

        // Notify the manager about permission change
        $this->notificationService->sendToUser($user->id, 'project_permission_changed', [
            'title' => $newPermission ? 'Project Creation Permission Granted' : 'Project Creation Permission Revoked',
            'message' => $newPermission
                ? "You can now create projects in the system."
                : "Your project creation permission has been revoked.",
            'action_url' => $newPermission ? route('manager.projects.create') : route('manager.projects.index'),
            'icon' => $newPermission ? 'fas fa-check-circle' : 'fas fa-times-circle',
            'color' => $newPermission ? 'green' : 'red',
        ]);

        $message = $newPermission
            ? "Project creation permission granted to {$user->name}"
            : "Project creation permission revoked from {$user->name}";

        return redirect()->back()->with('success', $message);
    }
    // Show all users
   // In UserController - update index method
// In UserController - update index method
public function index(Request $request)
{
    $query = User::with('client');

    // Department filter
    if ($request->has('department') && $request->department != 'all') {
        $query->where('department', $request->department);
    }

    // Role filter
    if ($request->has('role') && $request->role != 'all') {
        $query->where('role', $request->role);
    }

    $users = $query->get();

    // Get unique departments from database (dynamic)
    $departments = User::whereNotNull('department')
                      ->where('department', '!=', '')
                      ->where('department', '!=', 'Not Assigned')
                      ->distinct()
                      ->orderBy('department')
                      ->pluck('department');

    return view('admin.users.index', compact('users', 'departments'));
}

    // Show create user form
  // In UserController - update create method
public function create()
{
    // Get existing departments for suggestions
    $departments = User::whereNotNull('department')
                      ->where('department', '!=', '')
                      ->where('department', '!=', 'Not Assigned')
                      ->distinct()
                      ->orderBy('department')
                      ->pluck('department');

    return view('admin.users.create', compact('departments'));
}
    // Store new user created by Super Admin
   // In UserController - update store method
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
        'role' => 'required|string',
        'department' => 'nullable|string|max:255',
        'phone' => 'nullable|string',
        'company' => 'nullable|string',
    ]);

    $userData = [
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => $request->role,
        'department' => $request->department ?? 'Not Assigned',
    ];

    // If role is client, create a client record
    if ($request->role === 'client') {
        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'status' => 'active',
        ]);

        $userData['client_id'] = $client->id;
    }

    // Create user
    User::create($userData);

    return redirect()->route('users.index')->with('success', 'User created successfully.');
}


   // In UserController - update edit method
public function edit(User $user)
{
    $client = null;

    // If user has a client role and client_id, fetch the client data
    if ($user->role === 'client' && $user->client_id) {
        $client = Client::find($user->client_id);
    }

    // Get existing departments for suggestions
    $departments = User::whereNotNull('department')
                      ->where('department', '!=', '')
                      ->where('department', '!=', 'Not Assigned')
                      ->distinct()
                      ->orderBy('department')
                      ->pluck('department');

    return view('admin.users.edit', compact('user', 'client', 'departments'));
}

public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'role' => 'required|in:super_admin,admin,manager,user,client',
        'department' => 'nullable|string|max:255', // ✅ DEPARTMENT ADD KARO
        'password' => 'nullable|min:6',
        'phone' => 'nullable|string|max:20',
        'company' => 'nullable|string|max:255',
    ]);

    $updateData = [
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
        'department' => $request->department ?? 'Not Assigned', // ✅ DEPARTMENT ADD KARO
        'phone' => $request->phone,
    ];

    // Update password if provided
    if ($request->filled('password')) {
        $updateData['password'] = Hash::make($request->password);
    }

    // Handle client assignment if role changes to client
    if ($request->role === 'client') {
        if ($user->client_id) {
            // Update existing client
            $client = Client::find($user->client_id);
            if ($client) {
                $client->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'company' => $request->company,
                ]);
            }
        } else {
            // Create new client
            $client = Client::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'status' => 'active',
            ]);
            $updateData['client_id'] = $client->id;
        }
    } elseif ($request->role !== 'client' && $user->client_id) {
        // Remove client association if role changes from client
        $updateData['client_id'] = null;
    }

    $user->update($updateData);

    return redirect()->route('users.index')->with('success', 'User updated successfully.');
}

    public function destroy(User $user)
    {
        // Optional: Delete associated client if this is a client user
        if ($user->isClient() && $user->client) {
            $user->client->delete();
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }


       public function show(User $user)
    {
        $client = null;

        // If user has a client role and client_id, fetch the client data
        if ($user->role === 'client' && $user->client_id) {
            $client = \App\Models\Client::find($user->client_id);
        }

        return view('admin.users.show', compact('user', 'client'));
    }
}
