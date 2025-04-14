<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            "claim_files", "create_order", "edit_order", "order_index", "permission_create", "permission_delete",
            "permission_edit", "permission_index", "role_create", "role_delete", "role_edit", "role_index", "update_file_status",
            "upload_files", "user_create", "user_delete", "user_edit", "user_index", "view_dashboard", "view_order"
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        Role::firstOrCreate(['name' => 'Admin'])
            ->givePermissionTo(Permission::all());

        Role::firstOrCreate(['name' => 'Employee_1'])
            ->givePermissionTo(['view_order', 'claim_files', 'update_file_status', 'upload_files', 'view_dashboard']);

        Role::firstOrCreate(['name' => 'Employee_2'])
            ->givePermissionTo(['view_order', 'claim_files', 'update_file_status', 'view_dashboard']);
    }
}
