<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Show extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('show', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 50);
            $table->string('alias', 50);
            $table->string('genreid', 2);
            $table->text('description');
            $table->string('catid', 5);
            $table->string('image', 50);
            $table->string('publish', 2);
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
        Schema::drop('show');       
    }
}
