<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = "Permissions";
        $permissions = Permission::latest()->paginate(10);
        return view('permissions.index', compact('permissions', 'pageTitle'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'permissions' => 'required'
        ]);

        DB::beginTransaction();
        try {
            foreach (explode(',', $request->permissions) as $key => $permission) {
                if (!empty(trim($permission))) {
                    Permission::updateOrCreate([
                        'name' => trim($permission),
                        'guard_name' => 'web',
                    ], []);
                }
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Permissions Has been Added."
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        return response()->json($permission);
    }

    public function update(Request $request, Permission $permission)
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|unique:permissions,name,' . $permission->id
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $permission->update(['name' => $request->permissions]);
        return response()->json(['success' => true, 'message' => 'Permissions has been updated']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(['message' => 'Permission deleted successfully']);
    }

}
