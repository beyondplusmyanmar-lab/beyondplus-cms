<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * App\Models\SiteSettings targets "site_settings", which no migration created.
 *
 * Columns are the model's $fillable. default_currency is an integer key: the
 * model relates hasOne(Currency::class, 'id', 'default_currency') and the
 * removed GeneralSettingRepo joined currency.id on it. The model sets
 * $timestamps = false, so this table has none.
 *
 * The module flags and shipping fields are typed from their use as settings
 * values; their exact width on an existing deployment should be reconciled
 * against the live DDL before anyone relies on them matching.
 *
 * Guarded with hasTable: inert where the table already exists.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('site_settings')) {
            return;
        }

        Schema::create('site_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('shipping_weight', 30)->nullable();
            $table->string('shipping_dimensions', 30)->nullable();
            $table->unsignedInteger('default_currency')->nullable();
            $table->unsignedInteger('default_language')->nullable();
            $table->boolean('coupon_module')->default(false);
            $table->boolean('promotion_module')->default(false);
            $table->boolean('reward_module')->default(false);
            $table->integer('stock_limit')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
