<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

use Mockery\Matcher\Closure;
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;
use SebastianBergmann\Exporter\Exception;

class Vehicle extends Eloquent {

  use SingleTableInheritanceTrait;

  protected $table = "vehicles";

  protected static $singleTableTypeField = 'type';

  protected static $persisted = ['color', 'owner_id'];

  protected static $singleTableSubclasses = [
    'Nanigans\SingleTableInheritance\Tests\Fixtures\MotorVehicle',
    'Nanigans\SingleTableInheritance\Tests\Fixtures\Bike'
  ];

  public function owner()
  {
    return $this->belongsTo('Nanigans\SingleTableInheritance\Tests\Fixtures\User');
  }

  // testing hook to manipulate protected static properties from a public context
  public static function withAllPersisted($persisted, $closure) {
    $oldPersisted = static::$allPersisted[get_called_class()];

    static::$allPersisted[get_called_class()] = $persisted;

    $result = null;
    try {
      $result = $closure();
    } catch(Exception $e) {

    }
    static::$allPersisted[get_called_class()] = $oldPersisted;
    return $result;
  }
} 