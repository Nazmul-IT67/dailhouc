<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vehicle_data', function (Blueprint $table) {
            $table->unsignedBigInteger('upholstery_id')->nullable()->change();
            $table->unsignedBigInteger('previous_owner_id')->nullable()->change();
            $table->unsignedBigInteger('num_of_door_id')->nullable()->change();
            $table->unsignedBigInteger('num_of_seats_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_data', function (Blueprint $table) {
            $table->unsignedBigInteger('upholstery_id')->nullable(false)->change();
            $table->unsignedBigInteger('previous_owner_id')->nullable(false)->change();
            $table->unsignedBigInteger('num_of_door_id')->nullable(false)->change();
            $table->unsignedBigInteger('num_of_seats_id')->nullable(false)->change();
        });
    }
};
