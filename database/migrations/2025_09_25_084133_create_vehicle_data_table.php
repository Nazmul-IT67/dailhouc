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
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('vehicle_conditions_id')->constrained('vehicle_conditions')->onDelete('cascade');
            $table->foreignId('body_color_id')->constrained('body_colors')->onDelete('cascade');
            $table->foreignId('upholstery_id')->constrained('upholsteries')->onDelete('cascade');
            $table->foreignId('interior_color_id')->constrained('interior_colors')->onDelete('cascade');
            $table->foreignId('previous_owner_id')->constrained('previous_owners')->onDelete('cascade');
            $table->foreignId('num_of_door_id')->constrained('number_of_doors')->onDelete('cascade');
            $table->foreignId('num_of_seats_id')->constrained('number_of_seats')->onDelete('cascade');

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
