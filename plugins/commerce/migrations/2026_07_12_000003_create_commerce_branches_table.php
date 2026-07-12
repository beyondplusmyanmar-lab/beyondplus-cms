<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Commerce branches — store locations shown in the theme's Locations slot. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commerce_branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('hours')->nullable();
            $table->text('map_embed')->nullable();        // Google Maps embed URL
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commerce_branches');
    }
};
