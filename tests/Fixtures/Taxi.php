<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

class Taxi extends MotorVehicle {

  public function setTypeAttribute($value){
    $this->attributes['type'] = $value;
  }

  public function getTypeAttribute($value){
    return ucfirst($value);
  }
}