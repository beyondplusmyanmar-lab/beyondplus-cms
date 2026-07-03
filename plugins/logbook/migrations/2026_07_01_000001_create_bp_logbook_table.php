<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Plugin-owned migration. Run when the Logbook plugin is activated and rolled
 * back when it is uninstalled.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bp_logbook', function (Blueprint $table) {
            $table->id();
            $table->string('event');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bp_logbook');
    }
};
