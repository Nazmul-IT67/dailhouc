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
            $table->unsignedBigInteger('sub_model_id')->nullable()->after('model_id');

            $table->foreign('sub_model_id')->references('id')->on('sub_models')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['sub_model_id']);
            $table->dropColumn('sub_model_id');
        });
    }
};
