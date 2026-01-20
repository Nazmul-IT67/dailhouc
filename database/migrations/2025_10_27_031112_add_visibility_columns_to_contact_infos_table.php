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
        Schema::table('contact_infos', function (Blueprint $table) {
            $table->boolean('is_email_show')->default(true);
            $table->boolean('is_number_show')->default(true);
            $table->boolean('is_whatsapp_show')->default(true);
            $table->string('whatsapp_country_code', 10)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('contact_infos', function (Blueprint $table) {
            $table->dropColumn([
                'is_email_show',
                'is_number_show',
                'is_whatsapp_show',
                'whatsapp_country_code',
            ]);
        });
    }
};
