<?php

use Illuminate\Database\Migrations\Migration;


class CreateFruitTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('fruits', function ($table){
      $table->increments('id');
      $table->integer('type');
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
    Schema::drop('fruit');
  }

}