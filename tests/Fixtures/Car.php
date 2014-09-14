<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

class Car extends MotorVehicle {

  protected static $singleTableType = 'car';
}