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
        Schema::table('events', function (Blueprint $table) {
            // Add new date fields as nullable first
            $table->dateTime('from_date')->nullable()->after('description');
            $table->dateTime('to_date')->nullable()->after('from_date');
        });
        
        // Migrate existing data
        \DB::table('events')->whereNotNull('event_date_time')->update([
            'from_date' => \DB::raw('event_date_time'),
            'to_date' => \DB::raw('event_date_time + interval \'1 day\'')
        ]);
        
        Schema::table('events', function (Blueprint $table) {
            // Make the new columns non-nullable
            $table->dateTime('from_date')->nullable(false)->change();
            $table->dateTime('to_date')->nullable(false)->change();
            
            // Drop the old event_date_time column
            $table->dropColumn('event_date_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Add back the old event_date_time column
            $table->dateTime('event_date_time')->nullable()->after('description');
            
            // Drop the new date fields
            $table->dropColumn(['from_date', 'to_date']);
        });
    }
};
