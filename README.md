Single Table Inheritance
========================


[![Build Status](https://travis-ci.org/Nanigans/single-table-inheritance.png?branch=master)](https://travis-ci.org/Nanigans/single-table-inheritance)
[![Latest Stable Version](https://poser.pugx.org/nanigans/single-table-inheritance/v/stable.svg)](https://packagist.org/packages/nanigans/single-table-inheritance)
[![Total Downloads](https://poser.pugx.org/nanigans/single-table-inheritance/downloads.svg)](https://packagist.org/packages/nanigans/single-table-inheritance)
[![Latest Unstable Version](https://poser.pugx.org/nanigans/single-table-inheritance/v/unstable.svg)](https://packagist.org/packages/nanigans/single-table-inheritance)
[![License](https://poser.pugx.org/nanigans/single-table-inheritance/license.svg)](https://packagist.org/packages/nanigans/single-table-inheritance)
[![Dependency Status](https://www.versioneye.com/php/nanigans:single-table-inheritance/badge.svg)](https://www.versioneye.com/php/nanigans:single-table-inheritance)

Single Table Inheritance is a trait for Laravel 4.2+ Eloquent models that allows multiple models to be stored in the same database table. We support a few key featres

 * Implemented as a Trait so that it plays nice with others, such as Laravel's `SoftDeletingTrait` or the excellent [Validating](https://github.com/dwightwatson/validating), without requiring a complicated mess of Eloquent Model subclasses.
 * Allow arbitrary class hierarchies not just two-level parent-child relationships. 
 * Customizable database column name that is used to store the model type.
 * Customizable string for the model type value stored in the database. (As opposed to forcing the use of the fully qualified model class name.)
 * Allow database rows that don't map to known model types. They will never be returned in queries.



# Installation
Simply add the package to your `composer.json` file and run `composer update`.

```
"nanigans/single-table-inheritance": "0.3.*"
```

Or go to your project directory where the `composer.json` file is located and type:

```sh
composer require "nanigans/single-table-inheritance:0.3.*"
```

# Basic Usage

Getting started with the Single Tabe Inheritance Trait is simple. Add the constraint and add a few properties to your models. A complete example of a `Vehicle` super class with two subclasses `Truck` and `Car` is given by 

```php
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;

class Vehicle extends Eloquent
{
  use SingleTableInheritanceTrait;

  protected $table = "vehicles";

  protected static $singleTableTypeField = 'type';

  protected static $singleTableSubclasses = [
    'Car',
    'Truck'
  ];
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

## Define the database table
In the root model set the `protected` property `$table` to define which database table to use to store all your classes.  
*Note:* even if you are using the default for the root class (i.e. the 'vehicles' table for the `Vehicle` class) this is required so that subclasses inherit the same setting rather than defaulting to their own table name.

## Define the databse column to store the class type
In the root model set the `protected static` proerty `$singleTableTypeField` to define which database column to use to store the type of each class.

## Define the subclasses
In the root model and each branch model define the `protected static` property `$singleTableSubclasses` to define which subclasses are part of the classes hierarchy.

## Define the values for class type 
In each concrete class set the `protected static` property `$singleTableType` to define the string value for this class that will be stored in the `$singleTableTypeField` database column.

# Advanced Usage

## Multi Level Class Hierarchies 

## Defining Which Atttributes Are Persisted

# Inspiration 

We've choosen a very particualr implementaton to support single table inheritence. However, others have written code and articles around a general approach that proved influential.

First, Mark Smith has an excellent article [Single Table Inheritance in Laravel 4](http://www.colorfultyping.com/single-table-inheritance-in-laravel-4/) amognst other things is intorduces the importance of querries returning objects of the correct type. Second, Jacopo Beschi wrote and extension of Eloquent's `Model, [Laravel-Single-Table-Inheritance] (https://github.com/intrip/laravel-single-table-inheritance)` that introduces the importance of being able to define which attributes each model persists.

The use of Traits was heavy influence by the Eloquent's `SoftDeletingTrait` and the excellent [Validating Trait](https://github.com/dwightwatson/validating). 

# Design Decisions


