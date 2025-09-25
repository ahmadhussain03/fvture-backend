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
        Schema::create('seatmaps', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('background_url');
            $table->integer('map_width');
            $table->integer('map_height');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seatmaps');
    }
};
