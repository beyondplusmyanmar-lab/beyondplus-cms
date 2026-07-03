<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBpAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Matches sample-data.sql and what AdminAuth queries (per-role rows).
        Schema::create('bp_access', function (Blueprint $table) {
            $table->increments('access_id');
            $table->integer('module_id');
            $table->integer('usertype');
            $table->boolean('canshow')->default(0);
            $table->boolean('cancreate')->default(0);
            $table->boolean('canedit')->default(0);
            $table->boolean('candelete')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bp_access');
    }
}
