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
        Schema::create('club_tables', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->decimal('base_price', 10, 2);
            $table->text('image_url');
            $table->string('active_shape_url', 255);
            $table->text('shape_url');
            $table->integer('capacity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_tables');
    }
};
