<?php

namespace Nanigans\SingleTableInheritance\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Bike;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Car;
use Nanigans\SingleTableInheritance\Tests\Fixtures\MotorVehicle;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Truck;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Vehicle;

use Nanigans\SingleTableInheritance\Tests\Fixtures\Fruit;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Apple;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Banana;

/**
 * Class SingleTableInheritanceTraitQueryTest
 *
 * A set of tests of the query methods added to by the SingleTableInheritanceTrait
 * These tests are mostly duplicative of the model and static tests but they prove the integration
 * of the Trait with key parts of the Eloquent ORM.
 *
 * @package Nanigans\SingleTableInheritance\Tests
 */
class SingleTableInheritanceTraitQueryTest extends TestCase {

  public function testQueryingOnRoot() {
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

    // integer types
    (new Apple())->save();
    (new Banana())->save();
    (new Banana())->save();
    
    $results = Fruit::all();
    $this->assertEquals(3, count($results));

    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Apple', $results[0]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Banana',$results[1]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Banana',$results[2]);
  }

  public function testQueryingOnChild() {
    (new MotorVehicle())->save();
    (new Car())->save();
    (new Truck())->save();
    (new Truck())->save();
    (new Bike())->save();

    $results = MotorVehicle::all();

    $this->assertEquals(4, count($results));

    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\MotorVehicle', $results[0]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Car',          $results[1]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Truck',        $results[2]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Truck',        $results[3]);
  }

  public function testQueryingOnLeaf() {

    (new MotorVehicle())->save();
    (new Car())->save();
    (new Truck())->save();
    (new Truck())->save();
    (new Bike())->save();

    $results = Car::all();

    $this->assertEquals(1, count($results));

    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Car',   $results[0]);
  }

  public function testFindHasToMatchType() {
    $car = new Car();
    $car->save();
    $carId = $car->id;

    $this->assertNull(Truck::find($carId));
  }

  public function testFindWorksThroughParentClass() {
    $car = new Car();
    $car->save();
    $carId = $car->id;

    $vehicle = Vehicle::find($carId);
    $this->assertNotNull($vehicle);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Car', $vehicle);
  }

  public function testIgnoreRowsWithMismatchingFieldType() {
    $now = Carbon::now();

    DB::table('vehicles')->insert([
      [
        'type'       => 'junk',
        'created_at' => $now,
        'updated_at' => $now
      ],
      [
        'type'       => 'car',
        'created_at' => $now,
        'updated_at' => $now
      ]
    ]);

    $results = Vehicle::all();
    $this->assertEquals(1, count($results));

    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Car', $results[0]);
  }

  public function testOnlyPersistedAttributesAreReturnedInQuery() {
    $now = Carbon::now();

    DB::table('vehicles')->insert([
      [
        'type'       => 'car',
        'color'      => 'red',
        'cruft'      => 'red is my favorite',
        'owner_id'   => null,
        'created_at' => $now,
        'updated_at' => $now
      ]
    ]);

    $car = Car::all()->first();

    $this->assertNull($car->cruft);
  }

  public function testPersistedAttributesCanIncludeBelongsToForeignKeys() {
    $now = Carbon::now();

    $userId = DB::table('users')->insert([
      [
        'name'       => 'Mickey Mouse',
        'created_at' => $now,
        'updated_at' => $now
      ]
    ]);

    DB::table('vehicles')->insert([
      [
        'type'       => 'car',
        'color'      => 'red',
        'owner_id'   => $userId,
        'created_at' => $now,
        'updated_at' => $now
      ]
    ]);

    $car = Car::all()->first();

    $this->assertEquals($userId, $car->owner()->first()->id);
  }

  public function testEmptyPersistedAttributesReturnsEverythingInQuery() {
    $now = Carbon::now();

    DB::table('vehicles')->insert([
      [
        'type'       => 'car',
        'color'      => 'red',
        'cruft'      => 'red is my favorite',
        'owner_id'   => null,
        'created_at' => $now,
        'updated_at' => $now
      ]
    ]);

    $car = Car::withAllPersisted([], function() {
      return Car::all()->first();
    });

    $this->assertEquals('red is my favorite', $car->cruft);
  }

  /**
   * @expectedException \Nanigans\SingleTableInheritance\Exceptions\SingleTableInheritanceException
   */
  public function testQueryThrowsExceptionIfConfigured() {
    $now = Carbon::now();

    DB::table('vehicles')->insert([
      [
        'type'       => 'bike',
        'color'      => 'red',
        'cruft'      => 'red is my favorite',
        'created_at' => $now,
        'updated_at' => $now
      ]
    ]);

    Bike::all()->first();
  }

  public function testUpdateRemovesScope() {
    $car = new Car();
    $car->color = 'red';
    $car->save();

    $dbCar = Vehicle::where('color', 'red')->first();
    $dbCar->color = 'green';
    $this->assertTrue($dbCar->save()); // if the scope doesn't remove bindings this save will throw an exception.
  }
} 