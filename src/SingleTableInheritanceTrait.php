<?php

namespace Nanigans\SingleTableInheritance;

use Nanigans\SingleTableInheritance\Exceptions\SingleTableInheritanceException;

trait SingleTableInheritanceTrait {

  private $subclassTypes = null;

  protected static $singleTableTypeMap = [];

  /**
   * Boot the trait. Adds an saving event to set the type field.
   *
   * @return void
   */
  public static function bootSingleTableInheritanceTrait() {

    static::getSingleTableTypeMap();

    static::addGlobalScope(new SingleTableInheritanceScope);

    static::observe(new SingleTableInheritanceObserver());
  }

  /**
   * Get the map of type field values to class names.
   * @return array the type map
   */
  public static function getSingleTableTypeMap() {
    $calledClass = get_called_class();

    if (array_key_exists($calledClass, self::$singleTableTypeMap) ){
      return self::$singleTableTypeMap[$calledClass];
    }

    $typeMap = [];

    // Check if the calledClass is a leaf of the hierarchy. singleTableSubclasses will be inherited from the parent class
    // so its important we check for the tableType first otherwise we'd infinitely recurse.
    if (property_exists($calledClass, 'singleTableType')) {
      $classType = static::$singleTableType;
      $typeMap[$classType] = $calledClass;
    } else if (property_exists($calledClass, 'singleTableSubclasses')) {
      foreach (static::$singleTableSubclasses as $subclass) {
        $typeMap = array_merge($typeMap, $subclass::getSingleTableTypeMap());
      }
    }

    self::$singleTableTypeMap[$calledClass] = $typeMap;

    return $typeMap;
  }

  /**
   * Get the list of all types in the hierarchy.
   * @return array the list of type strings
   */
  public function getSingleTableTypes() {
    return array_keys(static::getSingleTableTypeMap());
  }

  /**
   * Set the type value into the type field attribute
   * @throws Exceptions\SingleTableInheritanceException
   */
  public function setSingleTableType() {
    $modelClass = get_class($this);
    $classType = property_exists($modelClass, 'singleTableType') ? $modelClass::$singleTableType : null;
    if ($classType) {
      $this->{static::$singleTableTypeField} = $classType;
    } else {
      // We'd like to be able to declare non-leaf classes in the hierarchy as abstract so they can't be instantiated and saved.
      // However, Eloquent expects to instantiate classes at various points. Therefore throw an exception if we try to save
      // and instance that doesn't have a type.
      throw new SingleTableInheritanceException('Cannot save Single table inheritance model without declaring static property $singleTableType.');
    }
  }

  /**
   * Override the Eloquent method to construct a model of the type given by the value of singleTableTypeField
   * @param array $attributes
   */
  public function newFromBuilder($attributes = array()) {
    $typeField = static::$singleTableTypeField;

    $classType = $attributes->$typeField;

    if ($classType) {
      $childTypes = static::getSingleTableTypeMap();
      if (array_key_exists($classType, $childTypes)) {
        $class = $childTypes[$classType];
        $instance = (new $class)->newInstance([], true);
        $instance->setRawAttributes((array) $attributes, true);
        return $instance;
      } else {
        // Throwing either of the exceptions suggests something has gone very wrong with the Global Scope
        // There is not graceful recovery so complain loudly.
        throw new SingleTableInheritanceException("Cannot construct newFromBuilder for unrecognized $typeField=$classType");
      }
    } else {
      throw new SingleTableInheritanceException("Cannot construct newFromBuilder without a value for $typeField");
    }
  }

  /**
   * Get the qualified name of the column used to store the class type.
   * @return string the qualified column name
   */
  public function getQualifiedSingleTableTypeColumn() {
    return $this->getTable() . '.' . static::$singleTableTypeField;
  }
} 