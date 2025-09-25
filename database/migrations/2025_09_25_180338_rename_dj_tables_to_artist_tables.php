<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename djs table to artists
        Schema::rename('djs', 'artists');
        
        // Rename event_dj table to event_artist
        Schema::rename('event_dj', 'event_artist');
        
        // Update foreign key column names in event_artist table
        Schema::table('event_artist', function (Blueprint $table) {
            $table->renameColumn('dj_id', 'artist_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert foreign key column names in event_artist table
        Schema::table('event_artist', function (Blueprint $table) {
            $table->renameColumn('artist_id', 'dj_id');
        });
        
        // Rename event_artist table back to event_dj
        Schema::rename('event_artist', 'event_dj');
        
        // Rename artists table back to djs
        Schema::rename('artists', 'djs');
    }
};
