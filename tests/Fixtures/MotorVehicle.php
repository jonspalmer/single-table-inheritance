<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

class MotorVehicle extends Vehicle {

  protected static $singleTableType = 'motorvehicle';

  protected static $persisted = ['fuel'];

  protected static $singleTableSubclasses = [
    'Nanigans\SingleTableInheritance\Tests\Fixtures\Car',
    'Nanigans\SingleTableInheritance\Tests\Fixtures\Truck'
  ];
}