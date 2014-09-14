<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Vehicle extends Eloquent {

  use SingleTableInheritanceTrait;

  protected $table = "vehicles";

  protected static $singleTableTypeField = 'type';

  protected static $singleTableSubclasses = [
    'Nanigans\SingleTableInheritance\Tests\Fixtures\MotorVehicle',
    'Nanigans\SingleTableInheritance\Tests\Fixtures\Bike'
  ];
} 