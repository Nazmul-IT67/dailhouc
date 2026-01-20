<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('engine_and_environments', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_type_id')->nullable()->change();
            $table->unsignedBigInteger('num_of_gears_id')->nullable()->change();
            $table->unsignedBigInteger('cylinders_id')->nullable()->change();
            $table->unsignedBigInteger('emission_classes_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('engine_and_environments', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_type_id')->nullable(false)->change();
            $table->unsignedBigInteger('num_of_gears_id')->nullable(false)->change();
            $table->unsignedBigInteger('cylinders_id')->nullable(false)->change();
            $table->unsignedBigInteger('emission_classes_id')->nullable(false)->change();
        });
    }
};
