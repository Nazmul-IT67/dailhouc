<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreignId('fuel_id')
                ->nullable()   // <-- important
                ->after('body_type_id')
                ->constrained('fuels')
                ->onDelete('cascade');
        });
    }



    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['fuel_id']);
            $table->dropColumn('fuel_id');
        });
    }
};
