<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

class Truck extends MotorVehicle {

  protected static $singleTableType = 'truck';
}