<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->paginate(20);
        $pageTitle = "Roles";
        return view('roles.index', compact('roles', 'pageTitle'));
    }

    public function create()
    {
        $pageTitle = "Create Role";
        $permissions = Permission::all();
        return view('roles.create', compact('permissions', 'pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array|nullable',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $request->name]);
            $role->syncPermissions($request->permissions);

            DB::commit();
            return redirectBackWithSuccess('Role created successfully.', 'roles.index');
        } catch (\Throwable $th) {
            return backWithError($th->getMessage());
        }
    }

    public function edit(Role $role)
    {
        $pageTitle = "Edit Role";
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions', 'pageTitle'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array|nullable',
        ]);

        DB::beginTransaction();
        try {
            $role->update(['name' => $request->name]);
            $role->syncPermissions($request->permissions);

            DB::commit();
            return redirectBackWithSuccess('Role updated successfully.', 'roles.index');
        } catch (\Throwable $th) {
            return backWithError($th->getMessage());
        }
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}

