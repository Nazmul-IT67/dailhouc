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
        Schema::create('number_of_seats', function (Blueprint $table) {
            $table->id();
            $table->integer('number')->unique(); // Number of seats (e.g., 2, 4, 5)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('number_of_seats');
    }
};
