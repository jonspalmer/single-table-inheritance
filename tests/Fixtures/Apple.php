<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

class Apple extends Fruit {

  protected static $singleTableType = FruitType::APPLE;
}