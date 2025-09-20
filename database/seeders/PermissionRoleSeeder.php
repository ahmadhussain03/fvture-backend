<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Permission management
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            
            // Blog management
            'view blogs',
            'create blogs',
            'edit blogs',
            'delete blogs',
            
            // Category management
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Tag management
            'view tags',
            'create tags',
            'edit tags',
            'delete tags',
            
            // Event management
            'view events',
            'create events',
            'edit events',
            'delete events',
            
            // DJ management
            'view djs',
            'create djs',
            'edit djs',
            'delete djs',
            
            // File management
            'upload files',
            'delete files',
            
            // System settings
            'view settings',
            'edit settings',
        ];

        // Create permissions (only if they don't exist)
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles and their permissions
        $roles = [
            'super-admin' => $permissions, // Super admin gets all permissions
            'admin' => [
                'view users', 'create users', 'edit users',
                'view roles', 'view permissions',
                'view blogs', 'create blogs', 'edit blogs', 'delete blogs',
                'view categories', 'create categories', 'edit categories', 'delete categories',
                'view tags', 'create tags', 'edit tags', 'delete tags',
                'view events', 'create events', 'edit events', 'delete events',
                'view djs', 'create djs', 'edit djs', 'delete djs',
                'upload files', 'delete files',
                'view settings', 'edit settings',
            ],
            'editor' => [
                'view blogs', 'create blogs', 'edit blogs',
                'view categories', 'create categories', 'edit categories',
                'view tags', 'create tags', 'edit tags',
                'view events', 'create events', 'edit events',
                'view djs', 'create djs', 'edit djs',
                'upload files',
            ],
            'author' => [
                'view blogs', 'create blogs', 'edit blogs',
                'view categories', 'view tags',
                'view events', 'create events', 'edit events',
                'view djs', 'create djs', 'edit djs',
                'upload files',
            ],
            'user' => [
                'view blogs',
                'view categories', 'view tags',
                'view events', 'view djs',
            ],
        ];

        // Create roles and assign permissions (only if they don't exist)
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            
            // Get permission models for this role
            $permissionModels = Permission::whereIn('name', $rolePermissions)->get();
            
            // Sync permissions to role (only if role doesn't have permissions yet)
            if ($role->permissions()->count() === 0) {
                $role->syncPermissions($permissionModels);
            }
        }

        $this->command->info('✅ Permissions and roles created/updated successfully.');
    }
}