Single Table Inheritance
========================


[![Build Status](https://travis-ci.org/Nanigans/single-table-inheritance.png?branch=master)](https://travis-ci.org/Nanigans/single-table-inheritance)
[![Latest Stable Version](https://poser.pugx.org/nanigans/single-table-inheritance/v/stable.svg)](https://packagist.org/packages/nanigans/single-table-inheritance)
[![Total Downloads](https://poser.pugx.org/nanigans/single-table-inheritance/downloads.svg)](https://packagist.org/packages/nanigans/single-table-inheritance)
[![Latest Unstable Version](https://poser.pugx.org/nanigans/single-table-inheritance/v/unstable.svg)](https://packagist.org/packages/nanigans/single-table-inheritance)
[![License](https://poser.pugx.org/nanigans/single-table-inheritance/license.svg)](https://packagist.org/packages/nanigans/single-table-inheritance)
[![Dependency Status](https://www.versioneye.com/php/nanigans:single-table-inheritance/0.2.0/badge.svg)](https://www.versioneye.com/php/nanigans:single-table-inheritance/0.2.0)

Single Table Inheritance is a trait for Laravel 4.2+ Eloquent models that allows multiple models to be stored in the same database table. We support a few key featres

 * Implemented as a Trait so that it plays nice with others, such as Laravel's `SoftDeletingTrait` or the excellent [Validating](https://github.com/dwightwatson/validating), without requiring a complicated mess of Eloquent Model subclasses.
 * Allow arbitrary class hierarchies not just two-level parent-child relationships. 
 * Customizable database column name that is used to store the model type.
 * Customizable string for the model type value stored in the database. (As opposed to forcing the use of the fully qualified model class name.)
 * Allow database rows that don't map to known model types. They will never be returned in queries.



# Installation
Simply add the package to your `composer.json` file and run `composer update`.

```
"nanigans/single-table-inheritance": "0.1.*"
```

Or go to your project directory where the `composer.json` file is located and type:

```sh
composer require "nanigans/single-table-inheritance:0.1.*"
```

## Overview

First, add the trait to your root model and add a few properties that define the database table structure and class hierarchy.

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
```

Next, in the child classes declare the string value to store in the type field in the database.

```php

class Car extends Vehicle
{
	protected static $singleTableType = 'car';
}

class Truck extends Vehicle
{
	protected static $singleTableType = 'truck';
}
```

## Usage

The Single Table Inheritance Trait requires four properties to be declared in your classes:

 * `protected $table`  
   Declared in the root model to define which database table to use to store all you classes.  
   *Note:* even if you are using the default for the root class (i.e. the 'vehicles' table for the `Vehicle` class) this is required so that subclasses inherit the same setting rather than defaulting to their own table name.

 * `protected static $singleTableTypeField`  
   Declared in the root model to define which database column to use to store the type of the class.

 * `protected static $singleTableSubclasses`  
   Declared in the root model and each branch model to define which subclasses are part of the classes hierarchy.

 * `protected static $singleTableType`  
   Declared in each concrete model to define the string value for this class that will be stored in the `$singleTableTypeField` database column.

