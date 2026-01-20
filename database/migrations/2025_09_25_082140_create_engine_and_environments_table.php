<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('engine_and_environments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');

            $table->foreignId('driver_type_id')->constrained('driver_types')->onDelete('cascade');
            $table->foreignId('transmission_id')->constrained('transmissions')->onDelete('cascade');
            $table->foreignId('num_of_gears_id')->constrained('num_of_gears')->onDelete('cascade');
            $table->foreignId('cylinders_id')->constrained('cylinders')->onDelete('cascade');
            $table->foreignId('emission_classes_id')->constrained('emission_classes')->onDelete('cascade');

            $table->decimal('engine_size', 8, 2)->nullable();
            $table->decimal('kerb_weight', 8, 2)->nullable();
            $table->decimal('fuel_consumption_urban', 8, 2)->nullable();
            $table->decimal('fuel_consumption_combined', 8, 2)->nullable();
            $table->decimal('fuel_consumption_combined_gm', 8, 2)->nullable();
            $table->decimal('co2_emissions', 8, 2)->nullable();

            $table->boolean('catalytic_converter')->default(0);
            $table->boolean('particle_filter')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engine_and_environments');
    }
};
