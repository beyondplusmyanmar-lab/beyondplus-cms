<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Events carry a start time, not just a day — widen bp_posts.event_at from
 * date to datetime so the time entered in the admin is preserved.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bp_posts', function (Blueprint $table) {
            $table->dateTime('event_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bp_posts', function (Blueprint $table) {
            $table->date('event_at')->nullable()->change();
        });
    }
};
