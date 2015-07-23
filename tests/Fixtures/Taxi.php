<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

class Taxi extends MotorVehicle {

  protected static $singleTableType = 'taxi';

  public function setTypeAttribute($value){
    $this->attributes['type'] = $value;
  }

  public function GetTypeAttribute($value){
    return ucfirst($value);
  }
}