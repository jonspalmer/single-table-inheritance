<?php

namespace Phaza\SingleTableInheritance\Tests\Fixtures;

class Bike extends Vehicle {

  protected static $singleTableType = 'bike';

  protected static $throwInvalidAttributeExceptions = true;
}
