<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->unsignedBigInteger('transmission_id')->nullable()->change();
            $table->unsignedBigInteger('power_id')->nullable()->change();
            $table->unsignedBigInteger('equipment_line_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->unsignedBigInteger('transmission_id')->nullable(false)->change();
            $table->unsignedBigInteger('power_id')->nullable(false)->change();
            $table->unsignedBigInteger('equipment_line_id')->nullable(false)->change();
        });
    }
};
