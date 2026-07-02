<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bp_usertype', function (Blueprint $table) {
            $table->increments('id');
            $table->string('role', 100)->nullable();
            // These mirror the legacy schema (integer, not timestamp columns).
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bp_usertype');
    }
};
