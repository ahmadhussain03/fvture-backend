<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\User;
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
        // Create permissions for Blog model
        $blogPermissions = [
            'view-any Blog',
            'view Blog',
            'create Blog',
            'update Blog',
            'delete Blog',
            'restore Blog',
            'force-delete Blog',
            'replicate Blog',
            'reorder Blog',
        ];

        foreach ($blogPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $editorRole = Role::firstOrCreate(['name' => 'Editor']);
        $authorRole = Role::firstOrCreate(['name' => 'Author']);

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());
        
        $editorRole->givePermissionTo([
            'view-any Blog',
            'view Blog',
            'create Blog',
            'update Blog',
            'delete Blog',
        ]);

        $authorRole->givePermissionTo([
            'view-any Blog',
            'view Blog',
            'create Blog',
            'update Blog',
        ]);

        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'user_type' => 'admin',
            ]
        );

        $adminUser->assignRole('Super Admin');

        // Create editor user
        $editorUser = User::firstOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Editor User',
                'password' => bcrypt('password'),
                'user_type' => 'admin',
            ]
        );

        $editorUser->assignRole('Editor');

        // Create author user
        $authorUser = User::firstOrCreate(
            ['email' => 'author@example.com'],
            [
                'name' => 'Author User',
                'password' => bcrypt('password'),
                'user_type' => 'admin',
            ]
        );

        $authorUser->assignRole('Author');

        // Create some sample blog posts
        Blog::factory(10)->create([
            'user_id' => $adminUser->id,
        ]);

        Blog::factory(5)->draft()->create([
            'user_id' => $editorUser->id,
        ]);

        Blog::factory(3)->published()->create([
            'user_id' => $authorUser->id,
        ]);
    }
}
