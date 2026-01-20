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
        Schema::table('engine_and_environments', function (Blueprint $table) {
            $table->unsignedBigInteger('axle_count_id')->nullable()->after('id');

            // Optional: Add foreign key constraint
            $table->foreign('axle_count_id')->references('id')->on('axle_counts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('engine_and_environments', function (Blueprint $table) {
            $table->dropForeign(['axle_count_id']);
            $table->dropColumn('axle_count_id');
        });
    }
};
