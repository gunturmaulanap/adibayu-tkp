<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // 'role.view',
            // 'role.create',
            // 'role.edit',
            // 'role.delete',
            // 'user.view',
            // 'user.create',
            // 'user.edit',
            // 'user.delete',
            // 'item.view',
            // 'item.create',
            // 'item.edit',
            // 'item.delete',
            'sale.view',
            'sale.create',
            'sale.edit',
            'sale.delete',
            'sale.detail',
            'payment.view',
            'payment.create',
            'payment.edit',
            'payment.delete',
            'dashboard.view',

        ];

        foreach ($permissions as $key => $value) {
            Permission::create(['name' => $value]);
        }
    }
}
