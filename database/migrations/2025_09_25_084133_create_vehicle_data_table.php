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
        Schema::create('vehicle_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            
            $table->unsignedBigInteger('vehicle_conditions_id')->nullable();
            $table->unsignedBigInteger('body_color_id')->nullable();
            $table->unsignedBigInteger('upholstery_id')->nullable();
            $table->unsignedBigInteger('interior_color_id')->nullable();
            $table->unsignedBigInteger('previous_owner_id')->nullable();
            $table->unsignedBigInteger('num_of_door_id')->nullable();
            $table->unsignedBigInteger('num_of_seats_id')->nullable();

            $table->boolean('metalic')->default(0);
            $table->boolean('negotiable')->default(0);
            $table->boolean('indicate_vat')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_data');
    }
};
