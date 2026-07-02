<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('customer_types_id')->index('customers_customer_types_id_foreign');
            $table->string('first_name', 200)->nullable();
            $table->string('last_name', 200)->nullable();
            $table->string('gender', 30)->nullable();
            $table->string('date_of_birth', 200)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('password', 200)->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('subscribed_to_news_letter')->nullable();
            $table->tinyInteger('is_verified')->nullable();
            $table->string('profile_photo', 200)->nullable();
            $table->integer('total_reward_points')->nullable();
            $table->integer('total_subtotal_amount')->default(0);
            $table->integer('wallets')->default(0)->nullable();
            $table->date('reward_expiry_date')->nullable();
            $table->string('activation_code', 300)->nullable();
            $table->integer('otpcode')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->string('api_token', 64)->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
