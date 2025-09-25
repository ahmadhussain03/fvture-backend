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
        Schema::create('seatmap_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seatmap_id')->constrained('seatmaps')->onDelete('cascade');
            $table->foreignId('club_table_id')->constrained('club_tables')->onDelete('cascade');
            $table->integer('pos_x');
            $table->integer('pos_y');
            $table->integer('seat_width');
            $table->integer('seat_height');
            $table->string('seat_number', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seatmap_tables');
    }
};
