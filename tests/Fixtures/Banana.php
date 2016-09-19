<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

class Banana extends Fruit {

  protected static $singleTableType = FruitType::BANANA;
}