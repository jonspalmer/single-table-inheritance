<?php

namespace Phaza\SingleTableInheritance\Tests\Fixtures;

class Car extends MotorVehicle {

  protected static $singleTableType = 'car';

  protected static $persisted = ['capacity'];
}
