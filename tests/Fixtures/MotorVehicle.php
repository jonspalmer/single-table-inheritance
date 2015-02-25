<?php

namespace Phaza\SingleTableInheritance\Tests\Fixtures;

class MotorVehicle extends Vehicle {

  protected static $singleTableType = 'motorvehicle';

  protected static $persisted = ['fuel'];

  protected static $singleTableSubclasses = [
    'Phaza\SingleTableInheritance\Tests\Fixtures\Car',
    'Phaza\SingleTableInheritance\Tests\Fixtures\Truck'
  ];
}
