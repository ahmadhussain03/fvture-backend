<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove restore and force_delete permissions
        $permissionsToRemove = [
            'blog.restore',
            'blog.force_delete',
            'artist.restore',
            'artist.force_delete',
            'event.restore',
            'event.force_delete',
            'admin_user.restore',
            'admin_user.force_delete',
            'app_user.restore',
            'app_user.force_delete',
            'announcement.restore',
            'announcement.force_delete',
            'gallery.restore',
            'gallery.force_delete',
        ];

        foreach ($permissionsToRemove as $permissionName) {
            Permission::where('name', $permissionName)
                ->where('guard_name', 'web')
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the removed permissions
        $permissionsToRestore = [
            'blog.restore',
            'blog.force_delete',
            'artist.restore',
            'artist.force_delete',
            'event.restore',
            'event.force_delete',
            'admin_user.restore',
            'admin_user.force_delete',
            'app_user.restore',
            'app_user.force_delete',
            'announcement.restore',
            'announcement.force_delete',
            'gallery.restore',
            'gallery.force_delete',
        ];

        foreach ($permissionsToRestore as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                ['name' => $permissionName, 'guard_name' => 'web']
            );
        }
    }
};
