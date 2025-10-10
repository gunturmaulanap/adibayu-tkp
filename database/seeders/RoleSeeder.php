<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create admin and user roles
        $admin = Role::updateOrCreate(['name' => 'admin'], ['name' => 'admin']);
        $user = Role::updateOrCreate(['name' => 'user'], ['name' => 'user']);

        // assign all permissions to admin (if permissions exist)
        $all = Permission::all();
        if ($all->count()) {
            $admin->syncPermissions($all);
        }
    }
}
