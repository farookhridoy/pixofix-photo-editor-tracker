<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $employeeRole = Role::firstOrCreate(['name' => 'Employee_1']);

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@pixofix.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole($adminRole);

        // Create Employee User
        $employee = User::firstOrCreate(
            ['email' => 'employee@pixofix.com'],
            [
                'name' => 'Employee',
                'password' => Hash::make('password'),
            ]
        );
        $employee->assignRole($employeeRole);
    }
}
