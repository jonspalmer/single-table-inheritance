<?php

use Illuminate\Database\Migrations\Migration;

class CreatePublicationTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publications', function ($table){
            $table->increments('id');
            $table->string('name');
            $table->string('publisher_id');
            $table->string('type');
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
        Schema::drop('publications');
    }

}
