<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The App\Models\Apikeytable model, and the "api" auth guard behind it, have
 * always targeted a table that no migration created. Deployments have it;
 * a fresh install did not, so the repository could not stand up its own schema.
 *
 * The shape mirrors the users table: Apikeytable declares the same $fillable
 * and $hidden as App\User and extends the same Authenticatable base, so the
 * columns are taken from create_users_table rather than invented.
 *
 * Guarded with hasTable so it is inert on any database that already has the
 * table — an existing deployment is left exactly as it is.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bp_apikeytable')) {
            return;
        }

        Schema::create('bp_apikeytable', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('role')->default(1);
            $table->string('api_token', 60)->unique();
            $table->string('avatar', 100)->default('');
            $table->boolean('verified')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bp_apikeytable');
    }
};
