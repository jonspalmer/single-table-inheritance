<?php

namespace Nanigans\SingleTableInheritance\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Nanigans\SingleTableInheritance\Exceptions\SingleTableInheritanceException;
use Nanigans\SingleTableInheritance\Tests\Fixtures\User;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Bike;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Car;
use Nanigans\SingleTableInheritance\Tests\Fixtures\MotorVehicle;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Truck;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Vehicle;

/**
 * Class SingleTableInheritanceTraitQueryTest
 *
 * A set of tests for the behavior of Eloquent collections that are polymorphic.
 *
 * @package Nanigans\SingleTableInheritance\Tests
 */
class SingleTableInheritanceTraitCollectionTest extends TestCase {

  public function testRefresh() {
    (new MotorVehicle())->save();
    (new Car())->save();
    (new Truck())->save();
    (new Truck())->save();
    (new Bike())->save();

    $results = Vehicle::all();

    $this->assertEquals(5, count($results));

    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\MotorVehicle', $results[0]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Car',          $results[1]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Truck',        $results[2]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Truck',        $results[3]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Bike',         $results[4]);

    $results = $results->fresh();

    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\MotorVehicle', $results[0]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Car',          $results[1]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Truck',        $results[2]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Truck',        $results[3]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Bike',         $results[4]);
  }
}
