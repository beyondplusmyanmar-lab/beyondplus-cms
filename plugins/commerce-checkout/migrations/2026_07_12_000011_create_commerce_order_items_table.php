<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Line items — name/price are snapshotted at order time so later product edits
 *  don't rewrite historical orders. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commerce_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('name');
            $table->decimal('price', 12, 2)->default(0);
            $table->integer('qty')->default(1);
            $table->decimal('line_total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commerce_order_items');
    }
};
