<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

class MotorVehicle extends Vehicle {

  protected static $persisted = ['fuel'];

  protected static $singleTableSubclasses = [
    'car' => 'Nanigans\SingleTableInheritance\Tests\Fixtures\Car',
    'taxi' => 'Nanigans\SingleTableInheritance\Tests\Fixtures\Taxi',
    'truck' => 'Nanigans\SingleTableInheritance\Tests\Fixtures\Truck'
  ];
}