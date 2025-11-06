<?php

// php artisan db:seed --class=RolePermissionSeeder
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $agent = Role::firstOrCreate(['name' => 'agent']);

        $permissions = [
            'delete bookings',
            'manage users',
            'manage all bookings',
            
            'view global analytics',
            'cancel booking',
            'view dashboard',
            'manage bookings',
            'manage agents',
            'manage setting',
            'manage roles',
            'view bookings',
            'manage payment',
            'issue tickets',
            'manage airports',
            'download logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminPermissions = collect($permissions);
        $admin->syncPermissions($adminPermissions);

        $agent->syncPermissions(['view dashboard']);
    }
}