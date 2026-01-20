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
        Schema::create('profile_options', function (Blueprint $table) {
            $table->id();
            $table->string('group');    // e.g. 'ideal_connection', 'willing_to_relocate', 'age_preferences'
            $table->string('key');      // internal key/value (e.g. 'casual_intentional', '65_plus')
            $table->string('label');    // displayed label (e.g. 'Marriage-Minded', '65+')
            $table->text('info')->nullable(); // tooltip / ℹ️ text
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_options');
    }
};
