<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * App\Models\Currency targets "currency", which no migration created.
 *
 * Columns come from the model's $fillable plus the queries the (since removed)
 * GeneralSettingRepo ran against it: it selected id, name, code, symbol and
 * conversation_rate, and filtered on status = 'active' - so status is a string,
 * not a flag.
 *
 * Guarded with hasTable: inert where the table already exists.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('currency')) {
            return;
        }

        Schema::create('currency', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('code', 10);
            $table->string('symbol', 10)->nullable();
            $table->decimal('conversation_rate', 15, 6)->default(1);
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency');
    }
};
