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
        Schema::table('axle_counts', function (Blueprint $table) {
            // Drop the 'name' column if exists
            if (Schema::hasColumn('axle_counts', 'name')) {
                $table->dropColumn('name');
            }
            $table->integer('count')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('axle_counts', function (Blueprint $table) {
            // Add 'name' column back
            $table->string('name')->nullable();

            // Remove unique constraint from 'count'
            $table->dropUnique(['count']);
        });
    }
};
