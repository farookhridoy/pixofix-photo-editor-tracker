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
            'order_index', 'create_order','edit_order','view_order', 'upload_files',
            'claim_files', 'update_file_status', 'view_dashboard',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        Role::firstOrCreate(['name' => 'Admin'])
            ->givePermissionTo(Permission::all());

        Role::firstOrCreate(['name' => 'Employee_1'])
            ->givePermissionTo(['view_order', 'claim_files', 'update_file_status', 'upload_files']);

        Role::firstOrCreate(['name' => 'Employee_2'])
            ->givePermissionTo(['view_order', 'claim_files', 'update_file_status']);
    }
}
