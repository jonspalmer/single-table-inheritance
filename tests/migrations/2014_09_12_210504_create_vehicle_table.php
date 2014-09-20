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
      $table->string('color')->nullable();
      $table->string('fuel')->nullable();
      $table->integer('capacity')->nullable();
      $table->string('cruft')->nullable();
      $table->integer('owner_id')->nullable();
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
    Schema::drop('vehicles');
  }

}