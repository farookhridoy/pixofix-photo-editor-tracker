<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(20);
        $pageTitle = 'Users';
        return view('users.index', compact('users', 'pageTitle'));
    }

    public function create()
    { $pageTitle = 'Create User';
        $roles = Role::all();
        return view('users.create', compact('roles', 'pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'role' => 'required|exists:roles,name',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $user->assignRole($request->role);

            DB::commit();

            return redirectBackWithSuccess('User has been created successfully', 'users.index');
        } catch (\Throwable $th) {
            return backWithError($th->getMessage());
        }
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $pageTitle = 'Edit User';
        return view('users.edit', compact('user', 'roles', 'pageTitle'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $user->syncRoles([$request->role]);

            DB::commit();

            return redirectBackWithSuccess('User has been updated successfully', 'users.index');
        } catch (\Throwable $th) {

            return backWithError($th->getMessage());
        }
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index');
    }
}
