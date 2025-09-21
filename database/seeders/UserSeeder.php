<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define users to create
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@fvture.com',
                'password' => 'password123',
                'role' => 'Super Admin',
                'user_type' => 'admin',
            ],
            [
                'name' => 'Editor User',
                'email' => 'editor@fvture.com',
                'password' => 'password123',
                'role' => 'Editor',
                'user_type' => 'admin',
            ]
        ];

        foreach ($users as $userData) {
            // Check if user already exists
            $existingUser = User::where('email', $userData['email'])->first();
            
            if ($existingUser) {
                $this->command->info("User '{$userData['email']}' already exists, skipping...");
                continue;
            }

            // Create user
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'email_verified_at' => now(),
                'user_type' => $userData['user_type'],
            ]);

            // Assign role
            $role = Role::where('name', $userData['role'])->first();
            if ($role) {
                $user->assignRole($role);
                $this->command->info("✅ Created user '{$userData['email']}' with role '{$userData['role']}'");
            } else {
                $this->command->warn("⚠️ Role '{$userData['role']}' not found for user '{$userData['email']}'");
            }
        }

        $this->command->info('✅ Users seeded successfully.');
    }
}