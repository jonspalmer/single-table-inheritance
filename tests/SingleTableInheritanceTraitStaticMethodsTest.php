<?php

namespace Phaza\SingleTableInheritance\Tests;

use Illuminate\Support\Facades\DB;
use Phaza\SingleTableInheritance\Tests\Fixtures\Car;
use Phaza\SingleTableInheritance\Tests\Fixtures\MotorVehicle;
use Phaza\SingleTableInheritance\Tests\Fixtures\Vehicle;

/**
 * Class SingleTableInheritanceTraitStaticMethodsTest
 *
 * A set of tests of the static methods added to by the SingleTableInheritanceTrait
 *
 * @package Phaza\SingleTableInheritance\Tests
 */
class SingleTableInheritanceTraitStaticMethodsTest extends TestCase {

  // getSingleTableTypeMap

  public function testGetTypeMapOfRoot() {
    $expectedSubclassTypes = [
      'motorvehicle' => 'Phaza\SingleTableInheritance\Tests\Fixtures\MotorVehicle',
      'car'          => 'Phaza\SingleTableInheritance\Tests\Fixtures\Car',
      'truck'        => 'Phaza\SingleTableInheritance\Tests\Fixtures\Truck',
      'bike'         => 'Phaza\SingleTableInheritance\Tests\Fixtures\Bike',

    ];

    $this->assertEquals($expectedSubclassTypes, Vehicle::getSingleTableTypeMap());
  }

  public function testGetTypeMapOfChild() {
    $expectedSubclassTypes = [
      'motorvehicle' => 'Phaza\SingleTableInheritance\Tests\Fixtures\MotorVehicle',
      'car'          => 'Phaza\SingleTableInheritance\Tests\Fixtures\Car',
      'truck'        => 'Phaza\SingleTableInheritance\Tests\Fixtures\Truck',
    ];

    $this->assertEquals($expectedSubclassTypes, MotorVehicle::getSingleTableTypeMap());
  }

  public function testGetTypeMapOfLeaf() {

    $expectedSubclassTypes = [
      'car' => 'Phaza\SingleTableInheritance\Tests\Fixtures\Car'
    ];

    $this->assertEquals($expectedSubclassTypes, Car::getSingleTableTypeMap());
  }

  // getAllPersistedAttributes

  public function testGetAllPersistedOfRoot() {
    $a = Vehicle::getAllPersistedAttributes();
    sort($a);
    $this->assertEquals(['color', 'owner_id'], $a);
  }

  public function testGetAllPersistedOfChild() {
    $a = MotorVehicle::getAllPersistedAttributes();
    sort($a);
    $this->assertEquals(['color', 'fuel', 'owner_id'], $a);
  }

  public function testGetAllPersistedOfLeaf() {
    $a = Car::getAllPersistedAttributes();
    sort($a);
    $this->assertEquals(['capacity', 'color', 'fuel', 'owner_id'], $a);
  }
} 
