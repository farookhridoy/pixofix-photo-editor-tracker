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
    public function run()
    {
        $permissions = [
            "claim_files", "create_order", "edit_order", "order_index", "order_delete", "permission_create", "permission_delete",
            "permission_edit", "permission_index", "permission_show", "role_create", "role_delete", "role_edit", "role_index", "update_file_status",
            "upload_files", "user_create", "user_delete", "user_edit", "user_index", "view_dashboard", "view_order",
            "category_create", "category_delete", "category_edit", "category_index", "employee_order_index", "employee_order_edit", "employee_order_lock_file", "employee_order_claim_batch", "employee_my_batch_index",
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        Role::firstOrCreate(['name' => 'Admin'])
            ->givePermissionTo(Permission::whereNotIn('name', [
                "employee_order_index", "employee_order_edit", "employee_order_lock_file", "employee_order_claim_batch", "employee_my_batch_index", "update_file_status",'claim_files'
            ])->get());

        Role::firstOrCreate(['name' => 'Employee_1'])
            ->givePermissionTo(['view_order', 'claim_files', 'update_file_status', 'view_dashboard', "employee_order_index", "employee_order_edit", "employee_order_lock_file", "employee_order_claim_batch", "employee_my_batch_index",]);

        Role::firstOrCreate(['name' => 'Employee_2'])
            ->givePermissionTo(['view_order', 'claim_files', 'update_file_status', 'view_dashboard', "employee_order_index", "employee_order_edit", "employee_order_lock_file", "employee_order_claim_batch", "employee_my_batch_index",]);
    }
}
