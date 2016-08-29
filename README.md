Single Table Inheritance
========================


[![Build Status](https://travis-ci.org/Nanigans/single-table-inheritance.png?branch=master)](https://travis-ci.org/Nanigans/single-table-inheritance)
[![Latest Stable Version](https://poser.pugx.org/nanigans/single-table-inheritance/v/stable.svg)](https://packagist.org/packages/nanigans/single-table-inheritance)
[![Total Downloads](https://poser.pugx.org/nanigans/single-table-inheritance/downloads.svg)](https://packagist.org/packages/nanigans/single-table-inheritance)
[![Latest Unstable Version](https://poser.pugx.org/nanigans/single-table-inheritance/v/unstable.svg)](https://packagist.org/packages/nanigans/single-table-inheritance)
[![License](https://poser.pugx.org/nanigans/single-table-inheritance/license.svg)](https://packagist.org/packages/nanigans/single-table-inheritance)
[![Dependency Status](https://www.versioneye.com/php/nanigans:single-table-inheritance/badge.svg)](https://www.versioneye.com/php/nanigans:single-table-inheritance)

Single Table Inheritance is a trait for Laravel 5.2+ Eloquent models that allows multiple models to be stored in the same database table. We support a few key features

 * Implemented as a Trait so that it plays nice with others, such as Laravel's `SoftDeletingTrait` or the excellent [Validating](https://github.com/dwightwatson/validating), without requiring a complicated mess of Eloquent Model subclasses.
 * Allow arbitrary class hierarchies not just two-level parent-child relationships. 
 * Customizable database column name that is used to store the model type.
 * Customizable string for the model type value stored in the database. (As opposed to forcing the use of the fully qualified model class name.)
 * Allow database rows that don't map to known model types. They will never be returned in queries.



# Installation
Simply add the package to your `composer.json` file and run `composer update`.

```
"nanigans/single-table-inheritance": "0.7.*"
```

Or go to your project directory where the `composer.json` file is located and type:

```sh
composer require "nanigans/single-table-inheritance:0.7.*"
```

# Overview

Getting started with the Single Table Inheritance Trait is simple. Add the constraint and add a few properties to your models. A complete example of a `Vehicle` super class with two subclasses `Truck` and `Car` is given by

```php
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;

class Vehicle extends Model
{
  use SingleTableInheritanceTrait;

  protected $table = "vehicles";

  protected static $singleTableTypeField = 'type';

  protected static $singleTableSubclasses = [Car::class, Truck::class];
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

There are four required properties to be defined in your classes:

### Define the database table
In the root model set the `protected` property `$table` to define which database table to use to store all your classes.  
*Note:* even if you are using the default for the root class (i.e. the 'vehicles' table for the `Vehicle` class) this is required so that subclasses inherit the same setting rather than defaulting to their own table name.

### Define the database column to store the class type
In the root model set the `protected static` property `$singleTableTypeField` to define which database column to use to store the type of each class.

### Define the subclasses
In the root model and each branch model define the `protected static` property `$singleTableSubclasses` to define which subclasses are part of the classes hierarchy.

### Define the values for class type 
In each concrete class set the `protected static` property `$singleTableType` to define the string value for this class that will be stored in the `$singleTableTypeField` database column.



## Multi Level Class Hierarchies

It's not uncommon to have many levels in your class hierarchy. Its easy to define that structure by declaring subclasses at each level. For example suppose you have a Vehicle super class with two subclasses Bike and MotorVehicle. MotorVehicle in trun has two subclasses Car and Truck. You would define the classes like this:

```php
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;

class Vehicle extends Model
{
  use SingleTableInheritanceTrait;

  protected $table = "vehicles";

  protected static $singleTableTypeField = 'type';

  protected static $singleTableSubclasses = [MotorVehicle::class, Bike::class];
}

class MotorVehicle extends Vehicle
{
  protected static $singleTableSubclasses = [Car::class, Truck::class];
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

## Defining Which Attributes Are Persisted

Eloquent is extremely lenient in allowing you to get and set attributes. There is no mechanism to declare the set of attributes that a model supports. If you misuse and attribute it typically results in a SQL error if you try to issue an insert or update for a column that doesn't exist. By default the SingleTableInheritanceTrait operates the same way. However, when storing a class hierarchy in a single table there are often database columns that don't apply to all classes in the hierarchy. That Eloquent will store values in those columns makes it considerably easier to write bugs. There, the SingleTableInheritanceTrait allows you to define which attributes are persisted. The set of persisted attributes is also inherited from parent classes.

```php
class Vehicle extends Model
{
  protected static $persisted = ['color']
}

class MotorVehicle extends Vehicle
{
  protected static $persisted = ['fuel']
}
```

In the above example the class `Vehicle` would persist the attribute `color` and the class `MotorVehicle` would persist both `color` and `fuel`.

### Automatically Persisted Attributes

For convenience the model primary key and any dates are automatically added to the list of persisted attributes.

### BelongsTo Relations

If you are restricting the persisted attribute and your model has BelongsTo relations then you must include the foreign key column of the BelongsTo relation. For example:

```php
class Vehicle extends Model
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

BY default the SingleTableInheritanceTrait will handle invalid attributes silently It ignores non-persisted attributes when a model is saved and ignores non-persisted columns when hydrating a model from a builder query. However, you can force exceptions to be thrown when invalid attributes are encountered in either situation by setting the `$throwInvalidAttributeExceptions` property to true.

```php
/**
 * Whether the model should throw an InvalidAttributesException if non-persisted 
 * attributes are encountered when saving or hydrating a model.
 * If not set, it will default to false.
 *
 * @var boolean
 */
protected static $throwInvalidAttributeExceptions = true;
```

# Inspiration 

We've chosen a very particular implementation to support single table inheritance. However, others have written code and articles around a general approach that proved influential.

First, Mark Smith has an excellent article [Single Table Inheritance in Laravel 4](http://www.colorfultyping.com/single-table-inheritance-in-laravel-4/) amongst other things is introduces the importance of queries returning objects of the correct type. Second, Jacopo Beschi wrote and extension of Eloquent's `Model`, [Laravel-Single-Table-Inheritance](https://github.com/intrip/laravel-single-table-inheritance)`, that introduces the importance of being able to define which attributes each model persists.

The use of Traits was heavy influence by the Eloquent's `SoftDeletingTrait` and the excellent [Validating Trait](https://github.com/dwightwatson/validating). 




