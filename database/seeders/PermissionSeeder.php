<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for all operations
        $permissions = [
            // Blog permissions
            'blog.view_any',
            'blog.view',
            'blog.create',
            'blog.update',
            'blog.delete',
            'blog.restore',
            'blog.force_delete',
            // DJ permissions
            'dj.view_any',
            'dj.view',
            'dj.create',
            'dj.update',
            'dj.delete',
            'dj.restore',
            'dj.force_delete',
            // Event permissions
            'event.view_any',
            'event.view',
            'event.create',
            'event.update',
            'event.delete',
            'event.restore',
            'event.force_delete',
            // User permissions
            'user.view_any',
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
            // Role permissions
            'role.view_any',
            'role.view',
            'role.create',
            'role.update',
            'role.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        // Create Super Admin role
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'web'],
        );

        // Give Super Admin all permissions
        $superAdminRole->givePermissionTo(Permission::all());

        // Create additional roles for demonstration
        $editorRole = Role::firstOrCreate(
            ['name' => 'Editor', 'guard_name' => 'web'],
            ['name' => 'Editor', 'guard_name' => 'web']
        );
        $editorRole->givePermissionTo([
            'blog.view_any',
            'blog.view',
            'blog.create',
            'blog.update',
            'blog.delete',
            'dj.view_any',
            'dj.view',
            'dj.create',
            'dj.update',
            'dj.delete',
            'event.view_any',
            'event.view',
            'event.create',
            'event.update',
            'event.delete',
        ]);

        $viewerRole = Role::firstOrCreate(
            ['name' => 'Viewer', 'guard_name' => 'web'],
            ['name' => 'Viewer', 'guard_name' => 'web']
        );
        $viewerRole->givePermissionTo([
            'blog.view_any',
            'blog.view',
            'dj.view_any',
            'dj.view',
            'event.view_any',
            'event.view',
        ]);
    }
}