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
        Schema::table('body_types', function (Blueprint $table) {
            // Category relation (foreign key)
            $table->unsignedBigInteger('category_id')->after('id');
            $table->string('icon')->nullable()->after('title');

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('body_types', function (Blueprint $table) {
            // Rollback করার সময় কলাম ড্রপ করবে
            $table->dropForeign(['category_id']); // যদি foreign key add করো
            $table->dropColumn(['category_id', 'icon']);
        });
    }
};
