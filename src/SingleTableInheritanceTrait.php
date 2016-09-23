<?php

namespace Nanigans\SingleTableInheritance;

use Nanigans\SingleTableInheritance\Exceptions\SingleTableInheritanceException;
use Nanigans\SingleTableInheritance\Exceptions\SingleTableInheritanceInvalidAttributesException;

trait SingleTableInheritanceTrait {

  /**
   * A cache of all the class types strings to class names.
   * A map of model class name to map of type to subclass name.
   *
   * @var array
   */
  protected static $singleTableTypeMap = [];

  /**
   * A cache of all the persisted attributes associated of each class including super class attributes.
   * A map of model class name to attribute name array.
   *
   * @var array
   */
  protected static $allPersisted = [];

  /**
   * Boot the trait.
   *
   * @return void
   */
  public static function bootSingleTableInheritanceTrait() {

    static::getSingleTableTypeMap();
    static::getAllPersistedAttributes();

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
    }
    if (property_exists($calledClass, 'singleTableSubclasses')) {
      $subclasses = static::$singleTableSubclasses;
      // prevent infinite recursion if the singleTableSubclasses is inherited
      if (!in_array($calledClass, $subclasses)) {
        foreach ($subclasses as $subclass) {
          $typeMap = $typeMap + $subclass::getSingleTableTypeMap();
        }
      }
    }

    self::$singleTableTypeMap[$calledClass] = $typeMap;

    return $typeMap;
  }

  /**
   * Get all the persisted attributes that belongs to the class inheriting values declared on super classes
   *
   * @return array
   */
  public static function getAllPersistedAttributes() {
    $calledClass = get_called_class();

    if (array_key_exists($calledClass, self::$allPersisted)) {
      return self::$allPersisted[$calledClass];
    } else {
      $persisted = [];
      if(property_exists($calledClass, 'persisted')) {
        $persisted  = $calledClass::$persisted;
      }
      $parent = get_parent_class($calledClass);
      if (method_exists($parent, 'getAllPersistedAttributes')) {
        $persisted = array_merge($persisted, $parent::getAllPersistedAttributes());
      }
    }
    self::$allPersisted[$calledClass] = $persisted;
    return self::$allPersisted[$calledClass];
  }

  /**
   * Get the list of persisted attributes on this model inheriting values declared on super classes and
   * including the model's primary key and any date fields.
   * @return array
   */
  public function getPersistedAttributes() {
    $persisted = static::getAllPersistedAttributes();
    if (empty($persisted)) {
      // if the static persisted declaration is empty return empty
      return [];
    } else {
      // otherwise add the instance variables for primaryKey, typeField and dates
      return array_merge([$this->primaryKey, static::$singleTableTypeField], static::getAllPersistedAttributes(), $this->getDates());
    }
  }

  /**
   * Filter the attributes on the model. Any attribute that is not in the list of persisted attributes will be set to null.
   * Called before the model is saved to prevent setting spurious data in the database for columns belonging to other models.
   * If the flag $throwInvalidAttributeExceptions is set to true then this method will throw exceptions if it finds
   * attributes that are not expected to be persisted.
   */
  public function filterPersistedAttributes() {
    $persisted = $this->getPersistedAttributes();
    $extraAttributes = null;
    // if $persisted is empty we don't filter
    if (!empty($persisted)) {
      $extraAttributes = array_diff(array_keys($this->attributes), $this->getPersistedAttributes());

      if (!empty($extraAttributes)) {
        if ($this->getThrowInvalidAttributeExceptions()) {
          throw new SingleTableInheritanceInvalidAttributesException("Cannot save " . get_called_class() . ".", $extraAttributes);
        }
        foreach ($extraAttributes as $attribute) {
          unset($this->attributes[$attribute]);
        }
      }
    }
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
    if ($classType !== null) {
      if ($this->hasGetMutator(static::$singleTableTypeField)) {
        $this->{static::$singleTableTypeField} = $this->mutateAttribute(static::$singleTableTypeField, $classType);
      } else {
        $this->{static::$singleTableTypeField} = $classType;
      }
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
  public function newFromBuilder($attributes = array(), $connection = null) {
    $typeField = static::$singleTableTypeField;

    $classType = isset($attributes->$typeField) ? $attributes->$typeField : null;

    if ($classType !== null) {
      $childTypes = static::getSingleTableTypeMap();
      if (array_key_exists($classType, $childTypes)) {
        $class = $childTypes[$classType];
        $instance = (new $class)->newInstance([], true);
        $instance->setFilteredAttributes((array) $attributes);
        $instance->setConnection($connection ?: $this->connection);
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
    // mongodb doesn't work with "table_name"."field" queries; just "field"
    return $this->getConnection()->getDriverName() === "mongodb" ? static::$singleTableTypeField : $this->getTable() . '.' . static::$singleTableTypeField;
  }

  public function setFilteredAttributes(array $attributes) {
    $persistedAttributes = $this->getPersistedAttributes();
    if (empty($persistedAttributes)) {
      $filteredAttributes = $attributes;
    } else {
      // The query often include a 'select *' from the table which will return null for columns that are not persisted.
      // If any of those columns are non-null then we need to filter them our or throw and exception if configured.
      // array_flip is a cute way to do diff/intersection on keys by a non-associative array
      $extraAttributes = array_filter(array_diff_key($attributes, array_flip($persistedAttributes)), function($value) {
        return !is_null($value);
      });
      if (!empty($extraAttributes) && $this->getThrowInvalidAttributeExceptions()) {
        throw new SingleTableInheritanceInvalidAttributesException("Cannot construct " . get_called_class() . ".", $extraAttributes);
      }

      $filteredAttributes = array_intersect_key($attributes, array_flip($persistedAttributes));
    }

    $this->setRawAttributes($filteredAttributes, true);
  }

  protected function getThrowInvalidAttributeExceptions() {
    return property_exists(get_called_class(), 'throwInvalidAttributeExceptions') ? static::$throwInvalidAttributeExceptions : false;
  }
}
