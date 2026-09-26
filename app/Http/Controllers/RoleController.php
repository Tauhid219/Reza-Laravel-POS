<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of system roles and their permissions.
     */
    public function index(): View
    {
        $roles = Role::with(['permissions', 'users'])->get();
        $permissions = Permission::all()->groupBy(function ($perm) {
            $parts = explode('.', $perm->name);
            return count($parts) > 1 ? $parts[0] : 'general';
        });

        return view('roles.index', compact('roles', 'permissions'));
    }
}
