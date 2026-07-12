<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Commerce promotions — campaigns shown in the theme's Promotions slot. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commerce_promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->string('badge')->nullable();          // e.g. "-20%", "New"
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commerce_promotions');
    }
};
