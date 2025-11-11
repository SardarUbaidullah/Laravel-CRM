<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Show all users
    public function index()
    {
        $users = User::with('client')->get();
        return view('admin.users.index', compact('users'));
    }

    // Show create user form
    public function create()
    {
        return view('admin.users.create');
    }

    // Store new user created by Super Admin
   public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
        'role' => 'required|string',
        'phone' => 'nullable|string',
        'company' => 'nullable|string',
    ]);

    $userData = [
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => $request->role,
    ];

    // If role is client, create a client record
    if ($request->role === 'client') {
        Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'status' => 'active',
        ]);
    }

    // Create user without linking client_id
    User::create($userData);

    return redirect()->route('users.index')->with('success', 'User created successfully.');
}


    public function edit(User $user)
{
    $client = null;

    // If user has a client role and client_id, fetch the client data
    if ($user->role === 'client' && $user->client_id) {
        $client = Client::find($user->client_id);
    }

    return view('admin.users.edit', compact('user', 'client'));
}

public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'role' => 'required|in:super_admin,admin,manager,user,client',
        'password' => 'nullable|min:6',
        'phone' => 'nullable|string|max:20',
        'company' => 'nullable|string|max:255',
    ]);

    $updateData = [
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
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

    return redirect()->route('
    users.index')->with('success', 'User updated successfully.');
}

    public function destroy(User $user)
    {
        // Optional: Delete associated client if this is a client user
        if ($user->isClient() && $user->client) {
            $user->client->delete();
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
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
