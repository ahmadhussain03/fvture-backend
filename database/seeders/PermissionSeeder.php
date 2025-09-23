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
            // Admin User permissions
            'admin_user.view_any',
            'admin_user.view',
            'admin_user.create',
            'admin_user.update',
            'admin_user.delete',
            'admin_user.restore',
            'admin_user.force_delete',
            // App User permissions
            'app_user.view_any',
            'app_user.view',
            'app_user.create',
            'app_user.update',
            'app_user.delete',
            'app_user.restore',
            'app_user.force_delete',
            // Announcement permissions
            'announcement.view_any',
            'announcement.view',
            'announcement.create',
            'announcement.update',
            'announcement.delete',
            'announcement.restore',
            'announcement.force_delete',
            // Gallery permissions
            'gallery.view_any',
            'gallery.view',
            'gallery.create',
            'gallery.update',
            'gallery.delete',
            'gallery.restore',
            'gallery.force_delete',
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
            'dj.view_any',
            'dj.view',
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