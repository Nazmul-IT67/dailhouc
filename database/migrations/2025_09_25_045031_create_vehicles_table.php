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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->foreignId('model_id')->constrained('car_models')->onDelete('cascade');
            $table->date('first_registration');
            $table->foreignId('body_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('transmission_id')->constrained()->onDelete('cascade');
            $table->foreignId('power_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipment_line_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_type_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
