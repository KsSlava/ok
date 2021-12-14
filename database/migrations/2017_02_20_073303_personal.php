<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Personal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
        Schema::create('personal', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('alias', 50);
            $table->text('description');
            $table->integer('catid')->unsigned();
            $table->foreign('catid')->references('id')->on('personal_cat');

            $table->string('image', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('personal');
    }
}
