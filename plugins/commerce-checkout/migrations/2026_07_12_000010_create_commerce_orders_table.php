<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Orders placed through the checkout (inquiry / cash-on-delivery — no payment data). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commerce_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('address');
            $table->text('note')->nullable();
            $table->string('status')->default('new');   // new | confirmed | completed | cancelled
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->integer('item_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commerce_orders');
    }
};
