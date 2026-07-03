<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Harden the bp_options key-value store: cap option_name at 191 (index-safe on
 * all MySQL versions, and long enough for namespaced plugin keys like
 * plugin.<slug>.<field>) and make it UNIQUE so lookups are indexed and
 * updateOrCreate can't create duplicates. Runs before seeding, so the table is
 * empty when the unique index is added.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bp_options', function (Blueprint $table) {
            $table->string('option_name', 191)->change();
            $table->unique('option_name');
        });
    }

    public function down(): void
    {
        Schema::table('bp_options', function (Blueprint $table) {
            $table->dropUnique('bp_options_option_name_unique');
            $table->string('option_name')->change();
        });
    }
};
