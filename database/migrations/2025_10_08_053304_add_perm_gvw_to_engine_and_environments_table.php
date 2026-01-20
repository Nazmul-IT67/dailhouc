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
            $table->string('perm_gvw')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('engine_and_environments', function (Blueprint $table) {
            $table->dropColumn('perm_gvw');
        });
    }
};
