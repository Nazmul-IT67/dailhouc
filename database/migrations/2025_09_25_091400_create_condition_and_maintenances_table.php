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
        Schema::create('condition_and_maintenances', function (Blueprint $table) {
            $table->id();

            // Foreign key to vehicles
            $table->foreignId('vehicle_id')
                  ->constrained('vehicles')
                  ->onDelete('cascade');

            // Boolean fields with default 0
            $table->boolean('service_history')->default(0);
            $table->boolean('non_smoker_car')->default(0);
            $table->boolean('damaged_vehicle')->default(0);
            $table->boolean('guarantee')->default(0);

            // Date fields
            $table->date('recent_change_of_timing_belt')->nullable();
            $table->date('recent_technical_service')->nullable();
            $table->date('technical_inspection_valid_until')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('condition_and_maintenances');
    }
};
