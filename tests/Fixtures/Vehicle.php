<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

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

  public function owner() {
    return $this->belongsTo('Nanigans\SingleTableInheritance\Tests\Fixtures\User');
  }

  // testing hooks to manipulate protected properties from a public context
  public static function withAllPersisted($persisted, $closure) {
    $oldPersisted = static::$allPersisted[get_called_class()];

    static::$allPersisted[get_called_class()] = $persisted;

    $result = null;
    try {
      $result = $closure();
    } catch (Exception $e) {

    }
    static::$allPersisted[get_called_class()] = $oldPersisted;
    return $result;
  }

  public static function withTypeField($typeField, $closure) {
    $oldTypeField = static::$singleTableTypeField;
    static::$singleTableTypeField = $typeField;

    $result = null;
    try {
      $result = $closure();
    } catch (Exception $e) {

    }
    static::$singleTableTypeField = $oldTypeField;

    return $result;
  }

  public function setDates(array $dates) {
    $this->dates = $dates;
  }

  public function setPrimaryKey($primaryKey) {
    $this->primaryKey = $primaryKey;
  }

  public function setTable($table) {
    $this->table = $table;
  }
}