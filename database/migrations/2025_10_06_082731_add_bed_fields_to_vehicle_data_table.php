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
            $table->unsignedBigInteger('bed_count_id')->nullable();
            $table->unsignedBigInteger('bed_type_id')->nullable()->after('bed_count_id');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_data', function (Blueprint $table) {
            $table->dropColumn(['bed_count_id', 'bed_type_id']);
        });
    }
};
