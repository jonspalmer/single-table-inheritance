<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;
use SebastianBergmann\Exporter\Exception;

class Fruit extends Eloquent {

  use SingleTableInheritanceTrait;

  protected $table = "fruits";

  protected static $singleTableTypeField = 'type';

  protected static $singleTableSubclasses = [
    'Nanigans\SingleTableInheritance\Tests\Fixtures\Apple',
    'Nanigans\SingleTableInheritance\Tests\Fixtures\Banana'
  ];

}

class FruitType {
  const APPLE = 10;
  const BANANA = 22;
}