<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

class Car extends MotorVehicle {

  protected static $singleTableType = 'car';

  protected static $persisted = ['capacity'];
}