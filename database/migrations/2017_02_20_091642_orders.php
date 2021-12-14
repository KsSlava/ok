<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Orders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('showid', 5);
            $table->string('scenetype', 5);
            $table->string('sceneplace', 5);
            $table->timestamp('showdate');
            $table->string('publish', 2);   // enable/disable
            $table->string('price', 5);
            $table->string('type', 5);      //res, buy, all
            $table->string('token', 50);    // can be one for many tickets

            $table->timestamp('resbegin');
            $table->timestamp('resend');

            $table->string('payid');        // call back from paysystem
            $table->timestamp('paydate');

                         

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('orders');
    }
}
