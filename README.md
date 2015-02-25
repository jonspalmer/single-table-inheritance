Single Table Inheritance
========================

# Credit

This code is a fork of [Nanigans/single-table-inheritance](https://github.com/Nanigans/single-table-inheritance). I've only updated it to work with Laravel 5

[![Latest Stable Version](https://poser.pugx.org/phaza/single-table-inheritance/v/stable.svg)](https://packagist.org/packages/phaza/single-table-inheritance)
[![Total Downloads](https://poser.pugx.org/phaza/single-table-inheritance/downloads.svg)](https://packagist.org/packages/phaza/single-table-inheritance)
[![Latest Unstable Version](https://poser.pugx.org/phaza/single-table-inheritance/v/unstable.svg)](https://packagist.org/packages/phaza/single-table-inheritance)
[![License](https://poser.pugx.org/phaza/single-table-inheritance/license.svg)](https://packagist.org/packages/phaza/single-table-inheritance)

Single Table Inheritance is a trait for Laravel 5.0.6+ Eloquent models that allows multiple models to be stored in the same database table. We support a few key featres

 * Implemented as a Trait so that it plays nice with others, such as Laravel's `SoftDeletingTrait` or the excellent [Validating](https://github.com/dwightwatson/validating), without requiring a complicated mess of Eloquent Model subclasses.
 * Allow arbitrary class hierarchies not just two-level parent-child relationships. 
 * Customizable database column name that is used to store the model type.
 * Customizable string for the model type value stored in the database. (As opposed to forcing the use of the fully qualified model class name.)
 * Allow database rows that don't map to known model types. They will never be returned in queries.



# Installation
Simply add the package to your `composer.json` file and run `composer update`.

```
"phaza/single-table-inheritance": "1.0.*"
```

Or go to your project directory where the `composer.json` file is located and type:

```sh
composer require "phaza/single-table-inheritance:1.0.*"
```

# Overview

Getting started with the Single Tabe Inheritance Trait is simple. Add the constraint and add a few properties to your models. A complete example of a `Vehicle` super class with two subclasses `Truck` and `Car` is given by 

```php
use Phaza\SingleTableInheritance\SingleTableInheritanceTrait;

class Vehicle extends Eloquent
{
  use SingleTableInheritanceTrait;

  protected $table = "vehicles";

  protected static $singleTableTypeField = 'type';

  protected static $singleTableSubclasses = ['Car', 'Truck'];
}

class Car extends Vehicle
{
  protected static $singleTableType = 'car';
}

class Truck extends Vehicle
{
  protected static $singleTableType = 'truck';
}
```

There are four requred properties to be defined in your classes:

### Define the database table
In the root model set the `protected` property `$table` to define which database table to use to store all your classes.  
*Note:* even if you are using the default for the root class (i.e. the 'vehicles' table for the `Vehicle` class) this is required so that subclasses inherit the same setting rather than defaulting to their own table name.

### Define the databse column to store the class type
In the root model set the `protected static` proerty `$singleTableTypeField` to define which database column to use to store the type of each class.

### Define the subclasses
In the root model and each branch model define the `protected static` property `$singleTableSubclasses` to define which subclasses are part of the classes hierarchy.

### Define the values for class type 
In each concrete class set the `protected static` property `$singleTableType` to define the string value for this class that will be stored in the `$singleTableTypeField` database column.



## Multi Level Class Hierarchies

It's not uncommon to have many levels in your class hierarchy. Its easy to define that structure by declaring subclasses at each level. For example suppose you have a Vehicle super class with two subclasses Bike and MotorVehicle. MotorVehicle in trun has two subclasses Car and Truck. You would define the classes like this:

```php
use Phaza\SingleTableInheritance\SingleTableInheritanceTrait;

class Vehicle extends Eloquent
{
  use SingleTableInheritanceTrait;

  protected $table = "vehicles";

  protected static $singleTableTypeField = 'type';

  protected static $singleTableSubclasses = ['MotorVehicle', 'Bike'];
}

class MotorVehicle extends Vehicle
{
  protected static $singleTableSubclasses = ['Car', 'Truck'];
}

class Car extends MotorVehicle
{
  protected static $singleTableType = 'car';
}

class Truck extends MotorVehicle
{
  protected static $singleTableType = 'truck';
}

class Bike extends Vehicle
{
  protected static $singleTableType = 'bike';
}
```

## Defining Which Atttributes Are Persisted

Eloquent is extremly lenient in allowing you to get and set attributes. There is no mechanism to declare the set of attributes that a model supports. If you misues and attribute it typically results in a SQL error if you try to issue an insert or update for a column that doesn't exist. By default the SingleTableInheritanceTrait opperates the same way. However, when storing a class hierarchy in a single table there are often database columns that don't apply to all classes in the heirarchy. That Eloquent will store values in those columns makes it considerably easier to write bugs. There, the SingleTableInheritanceTrait allows you to define which attributes are persisted. The set of persisted attributes is also inherited from parent classes.

```php
class Vehicle extends Eloquent
{
  protected static $persisted = ['color']
}

class MotorVehicle extends Vehicle
{
  protected static $persisted = ['fuel']
}
```

In the above example the class `Vehicle` would persiste the attribute `color` and the class `MotorVehicle` would persiste both `color` and `fuel`.

### Automatically Persisted Attributes

For convineience the model primary key and any dates are automatically added to the list of persisted attributes.

### BelongsTo Relations

If you are restricting the persisted attribute and yoru model has BelongsTo relations then you must include the foreign key column of the BelongsTo relation. For example:

```php
class Vehicle extends Eloquent
{
  protected static $persisted = ['color', 'owner_id'];
  
  public function owner()
  {
    return $this->belongsTo('User', 'owner_id');
  }
}
```

Unfortunately there is no efficient way to automatically detect BelongsTo foreign keys.

### Throwing Exceptions for Invalid Attributes

BY default the SingleTableINheritanceTrait will handle invalid attributes silently It ignores non-persisted attributes when a model is saved and ignores non-persisted columns when hydrating a model from a builder query. However, you can force exceptions to be thrown when invalid attributes are encounted in either situation by setting the `$throwInvalidAttributeExceptions` property to true.

```php
/**
 * Whether the model should throw an InvalidAttributesException if non-persisted 
 * attributes are encoutered when saving or hydrating a model.
 * If not set, it will default to false.
 *
 * @var boolean
 */
protected static $throwInvalidAttributeExceptions = true;
```
