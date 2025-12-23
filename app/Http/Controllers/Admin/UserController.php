<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display users list
     */
    public function index()
    {
        $users = User::with('role')->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', Rules\Password::defaults()],
            'role_id'  => ['required', 'exists:roles,id'],
            'is_active'=> ['boolean'],
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'] ?? null,
            'password'  => Hash::make($validated['password']),
            'role_id'   => $validated['role_id'],
            'is_active' => $request->has('is_active'),
        ]);

        ActivityLog::log(
            'create',
            "Création de l'utilisateur: {$user->name}",
            'User',
            $user->id,
            ['email' => $user->email, 'role' => $user->role->name]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès!');
    }

    /**
     * Show edit user form
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::where('role_id', $user->role_id)->get();

        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', Rules\Password::defaults()],
            'role_id'  => ['required', 'exists:roles,id'],
            'is_active'=> ['boolean'],
        ]);

        $oldData = $user->only(['name', 'email', 'role_id', 'is_active']);

        $user->update([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'] ?? null,
            'role_id'   => $validated['role_id'],
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        ActivityLog::log(
            'update',
            "Modification de l'utilisateur: {$user->name}",
            'User',
            $user->id,
            ['old' => $oldData, 'new' => $user->only(['name', 'email', 'role_id', 'is_active'])]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès!');
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte!');
        }

        $userName = $user->name;
        $userId   = $user->id;

        ActivityLog::log(
            'delete',
            "Suppression de l'utilisateur: {$userName}",
            'User',
            $userId,
            ['email' => $user->email]
        );

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès!');
    }

    /**
     * Manage user permissions
     */
    public function permissions(User $user)
    {
        $modules     = ['books', 'sales', 'customers', 'suppliers', 'reports', 'settings'];
        $permissions = Permission::where('role_id', $user->role_id)->get();

        return view('admin.users.permissions', compact('user', 'modules', 'permissions'));
    }

    /**
     * Update user permissions
     */
    public function updatePermissions(Request $request, User $user)
    {
        $modules = ['books', 'sales', 'customers', 'suppliers', 'reports', 'settings'];

        foreach ($modules as $module) {
            Permission::updateOrCreate(
                ['role_id' => $user->role_id, 'module' => $module],
                [
                    'can_view'   => $request->has("permissions.{$module}.view"),
                    'can_create' => $request->has("permissions.{$module}.create"),
                    'can_edit'   => $request->has("permissions.{$module}.edit"),
                    'can_delete' => $request->has("permissions.{$module}.delete"),
                ]
            );
        }

        ActivityLog::log(
            'update_permissions',
            "Modification des permissions pour le rôle: {$user->role->display_name}",
            'User',
            $user->id,
            ['role' => $user->role->name]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Permissions mises à jour avec succès!');
    }

    /**
     * Toggle user active status
     */
    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $old = $user->is_active;
        $user->is_active = ! $user->is_active;
        $user->save();

        ActivityLog::log(
            'update',
            "Changement de statut de l'utilisateur: {$user->name}",
            'User',
            $user->id,
            ['old_is_active' => $old, 'new_is_active' => $user->is_active]
        );

        return back()->with('success', 'Statut de l’utilisateur mis à jour.');
    }
}
