<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Seed the application's roles and permissions.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'tournaments.view',
            'tournaments.follow',
            'tournaments.join',
            'tournaments.create',
            'tournaments.update',
            'tournaments.delete',
            'tournaments.manage_participants',
            'tournaments.report_results',
            'admin.users.manage',
            'admin.tournaments.moderate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $registeredUserRole = Role::firstOrCreate(['name' => 'registered_user', 'guard_name' => 'web']);
        $organizerRole = Role::firstOrCreate(['name' => 'organizer', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $registeredUserRole->syncPermissions([
            'tournaments.view',
            'tournaments.follow',
            'tournaments.join',
        ]);

        $organizerRole->syncPermissions([
            'tournaments.view',
            'tournaments.follow',
            'tournaments.join',
            'tournaments.create',
            'tournaments.update',
            'tournaments.delete',
            'tournaments.manage_participants',
            'tournaments.report_results',
        ]);

        $adminRole->syncPermissions(Permission::all());
    }
}
