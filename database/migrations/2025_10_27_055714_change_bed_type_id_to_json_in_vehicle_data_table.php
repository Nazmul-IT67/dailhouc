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
        Schema::table('vehicle_data', function (Blueprint $table) {
            $table->json('bed_type_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_data', function (Blueprint $table) {
            $table->bigInteger('bed_type_id')->nullable()->change();
        });
    }
};
