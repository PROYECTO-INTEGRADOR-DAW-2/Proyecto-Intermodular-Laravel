<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends BaseController
{
    /**
     * List all users with their roles (admin only).
     */
    public function users()
    {
        $users = User::with('roles')->get()->map(function ($user) {
            return [
                'id'    => $user->id,
                'name'  => $user->nombre . ' ' . $user->apellidos,
                'email' => $user->email,
                'role'  => $user->role,                  // direct column
                'roles' => $user->roles->pluck('name'),  // pivot roles
            ];
        });

        return $this->sendResponse($users, 'Users retrieved successfully');
    }

    /**
     * List all available roles.
     */
    public function roles()
    {
        $roles = Role::all(['id', 'name', 'description']);
        return $this->sendResponse($roles, 'Roles retrieved successfully');
    }

    /**
     * Assign a role to a user.
     */
    public function assignRole(Request $request, $userId)
    {
        $request->validate(['role' => 'required|string|exists:roles,name']);

        $user = User::findOrFail($userId);
        $role = Role::where('name', $request->role)->firstOrFail();

        if (!$user->roles()->where('role_id', $role->id)->exists()) {
            $user->roles()->attach($role->id);
        }

        // Also update the direct role column (use the highest-privilege role)
        $user->update(['role' => $request->role]);

        return $this->sendResponse(
            ['roles' => $user->roles()->pluck('name'), 'role' => $user->role],
            "Rol '{$request->role}' asignado correctamente"
        );
    }

    /**
     * Remove a role from a user.
     */
    public function removeRole($userId, $roleName)
    {
        $user = User::findOrFail($userId);
        $role = Role::where('name', $roleName)->firstOrFail();

        $user->roles()->detach($role->id);

        // Update the direct role column to the first remaining role, or 'user' if none
        $remainingRole = $user->roles()->first();
        $user->update(['role' => $remainingRole ? $remainingRole->name : 'user']);

        return $this->sendResponse(
            ['roles' => $user->roles()->pluck('name'), 'role' => $user->role],
            "Rol '{$roleName}' eliminado correctamente"
        );
    }
}
