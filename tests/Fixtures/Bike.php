<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

class Bike extends Vehicle {

  protected static $throwInvalidAttributeExceptions = true;
}