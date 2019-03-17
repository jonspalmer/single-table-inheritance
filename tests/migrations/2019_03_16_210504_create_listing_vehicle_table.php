<?php

use Illuminate\Database\Migrations\Migration;

class CreateListingVehicleTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('listing_vehicle', function ($table){
      $table->integer('vehicle_id');
      $table->integer('listing_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('listing_vehicle');
  }

}