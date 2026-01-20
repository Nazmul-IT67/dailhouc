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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('milage', 8, 2)->nullable();
            $table->text('description')->nullable();
            $table->json('equipment_ids')->nullable(); // store multiple equipment IDs as JSON
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['price', 'milage', 'description', 'equipment_ids']);
        });
    }
};
