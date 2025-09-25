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
            // Artist permissions
            'artist.view_any',
            'artist.view',
            'artist.create',
            'artist.update',
            'artist.delete',
            // Event permissions
            'event.view_any',
            'event.view',
            'event.create',
            'event.update',
            'event.delete',
            // Admin User permissions
            'admin_user.view_any',
            'admin_user.view',
            'admin_user.create',
            'admin_user.update',
            'admin_user.delete',
            // App User permissions
            'app_user.view_any',
            'app_user.view',
            'app_user.create',
            'app_user.update',
            'app_user.delete',
            // Announcement permissions
            'announcement.view_any',
            'announcement.view',
            'announcement.create',
            'announcement.update',
            'announcement.delete',
            // Gallery permissions
            'gallery.view_any',
            'gallery.view',
            'gallery.create',
            'gallery.update',
            'gallery.delete',
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
            'artist.view_any',
            'artist.view',
            'artist.create',
            'artist.update',
            'artist.delete',
            'event.view_any',
            'event.view',
            'event.create',
            'event.update',
            'event.delete',
            'admin_user.view_any',
            'admin_user.view',
            'admin_user.create',
            'admin_user.update',
            'admin_user.delete',
            'app_user.view_any',
            'app_user.view',
            'app_user.create',
            'app_user.update',
            'app_user.delete',
            'announcement.view_any',
            'announcement.view',
            'announcement.create',
            'announcement.update',
            'announcement.delete',
            'gallery.view_any',
            'gallery.view',
            'gallery.create',
            'gallery.update',
            'gallery.delete',
        ]);

        $viewerRole = Role::firstOrCreate(
            ['name' => 'Viewer', 'guard_name' => 'web'],
            ['name' => 'Viewer', 'guard_name' => 'web']
        );
        $viewerRole->givePermissionTo([
            'blog.view_any',
            'blog.view',
            'artist.view_any',
            'artist.view',
            'event.view_any',
            'event.view',
            'admin_user.view_any',
            'admin_user.view',
            'app_user.view_any',
            'app_user.view',
            'announcement.view_any',
            'announcement.view',
            'gallery.view_any',
            'gallery.view',
        ]);
    }
}