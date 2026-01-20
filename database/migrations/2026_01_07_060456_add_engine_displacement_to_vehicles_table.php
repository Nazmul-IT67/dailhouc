<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // 'nullable' মেথডটি ব্যবহার করা হয়েছে যাতে কলামটি খালি রাখা যায়
            $table->string('engine_displacement')->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('engine_displacement');
        });
    }
};
