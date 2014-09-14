<?php

use Illuminate\Database\Migrations\Migration;


class CreateVehicleTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('vehicles', function ($table){
      $table->increments('id');
      $table->string('type');
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('vehicles');
  }

}