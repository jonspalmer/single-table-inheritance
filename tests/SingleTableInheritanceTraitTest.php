<?php

namespace Nanigans\SingleTableInheritance\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Bike;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Car;
use Nanigans\SingleTableInheritance\Tests\Fixtures\MotorVehicle;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Truck;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Vehicle;
use Nanigans\SingleTableInheritance\Tests\Fixtures\User;

class SingleTableInheritanceTraitTest extends TestCase {

  public function testGetTypeMapOfRoot() {

    $expectedSubclassTypes = [
      'motorvehicle' => 'Nanigans\SingleTableInheritance\Tests\Fixtures\MotorVehicle',
      'car'          => 'Nanigans\SingleTableInheritance\Tests\Fixtures\Car',
      'truck'        => 'Nanigans\SingleTableInheritance\Tests\Fixtures\Truck',
      'bike'         => 'Nanigans\SingleTableInheritance\Tests\Fixtures\Bike',

    ];

    $this->assertEquals($expectedSubclassTypes, Vehicle::getSingleTableTypeMap());
  }

  public function testGetTypeMapOfChild() {

    $expectedSubclassTypes = [
      'motorvehicle' => 'Nanigans\SingleTableInheritance\Tests\Fixtures\MotorVehicle',
      'car'          => 'Nanigans\SingleTableInheritance\Tests\Fixtures\Car',
      'truck'        => 'Nanigans\SingleTableInheritance\Tests\Fixtures\Truck',
    ];

    $this->assertEquals($expectedSubclassTypes, MotorVehicle::getSingleTableTypeMap());
  }

  public function testGetTypeMapOfLeaf() {

    $expectedSubclassTypes = [
      'car' => 'Nanigans\SingleTableInheritance\Tests\Fixtures\Car'
    ];

    $this->assertEquals($expectedSubclassTypes, Car::getSingleTableTypeMap());
  }

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

  public function testGetPersistedOfRoot() {
    $a = (new Vehicle)->getPersistedAttributes();
    sort($a);
    $this->assertEquals(['color', 'created_at', 'id', 'owner_id', 'type', 'updated_at'], $a);
  }

  public function testGetPersistedOfChild() {
    $a = (new MotorVehicle)->getPersistedAttributes();
    sort($a);
    $this->assertEquals(['color', 'created_at', 'fuel', 'id', 'owner_id', 'type', 'updated_at'], $a);
  }

  public function testGetPersistedOfLeaf() {
    $a = (new Car)->getPersistedAttributes();
    sort($a);
    $this->assertEquals(['capacity', 'color', 'created_at', 'fuel', 'id', 'owner_id', 'type', 'updated_at'], $a);
  }

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

  public function testSavingThrowsExceptionIfModelHasNoClassType() {

    $this->setExpectedException('Nanigans\SingleTableInheritance\Exceptions\SingleTableInheritanceException');

    (new Vehicle())->save();
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

  public function testPersistedAttributesCanIncludeBelongsTOForeignKeys() {
    $now = Carbon::now();

    $userId = DB::table('users')->insert([
      [
        'name'       => 'Mickey Mouse',
        'created_at' => $now,
        'updated_at' => $now
      ]
    ]);

    echo "userID: $userId";

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

  public function testOnlyPersistedAttributesAreSaved() {
    $car = new Car;
    $car->color = 'red';
    $car->fuel = 'unleaded';
    $car->cruft = 'red is my favorite';

    $car->save();

    $dbCar = DB::table('vehicles')->first();

    $this->assertEquals($car->id, $dbCar->id);
    $this->assertNull($dbCar->cruft);

    $this->assertEquals('red', $dbCar->color);
    $this->assertEquals('unleaded', $dbCar->fuel);
  }

  public function testBelongsToRelationForeignKeyIsSaved() {
    $owner = new User;
    $owner->name = 'Mickey Mouse';
    $owner->save();

    $car = new Car;
    $car->color = 'red';
    $car->fuel = 'unleaded';
    $car->cruft = 'red is my favorite';
    $car->owner()->associate($owner);
    $car->save();

    $dbCar = DB::table('vehicles')->first();

    $this->assertEquals($car->id, $dbCar->id);
    $this->assertEquals($owner->id, $dbCar->owner_id);
  }

  public function testAllAttributesAreSavedIfPersistedIsEmpty() {
    $car = new Car;
    $car->color = 'red';
    $car->fuel = 'unleaded';
    $car->cruft = 'red is my favorite';

    Car::withAllPersisted([], function() use($car) {
      $car->save();
    });

    $dbCar = DB::table('vehicles')->first();

    $this->assertEquals($car->id, $dbCar->id);
    $this->assertEquals('red is my favorite', $dbCar->cruft);

    $this->assertEquals('red', $dbCar->color);
    $this->assertEquals('unleaded', $dbCar->fuel);
  }

  /**
   * @expectedException \Nanigans\SingleTableInheritance\Exceptions\SingleTableInheritanceException
   */
  public function testSaveThrowsExceptionIfConfigured() {
    $bike = new Bike;
    $bike->color = 'red';
    $bike->cruft = 'red is my favorite';
    $bike->save();
  }
} 