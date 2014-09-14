<?php

namespace Nanigans\SingleTableInheritance\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Bike;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Car;
use Nanigans\SingleTableInheritance\Tests\Fixtures\MotorVehicle;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Truck;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Vehicle;

class SingleTableInheritanceTraitTest extends TestCase {

  public function testGetTypeMapOfRoot() {

    $expectedSubclassTypes = [
      'car'   => 'Nanigans\SingleTableInheritance\Tests\Fixtures\Car',
      'truck' => 'Nanigans\SingleTableInheritance\Tests\Fixtures\Truck',
      'bike'  => 'Nanigans\SingleTableInheritance\Tests\Fixtures\Bike'
    ];

    $this->assertEquals($expectedSubclassTypes, Vehicle::getSingleTableTypeMap());
  }

  public function testGetTypeMapOfChild() {

    $expectedSubclassTypes = [
      'car'   => 'Nanigans\SingleTableInheritance\Tests\Fixtures\Car',
      'truck' => 'Nanigans\SingleTableInheritance\Tests\Fixtures\Truck',
    ];

    $this->assertEquals($expectedSubclassTypes, MotorVehicle::getSingleTableTypeMap());
  }

  public function testGetTypeMapOfLeaf() {

    $expectedSubclassTypes = [
      'car' => 'Nanigans\SingleTableInheritance\Tests\Fixtures\Car'
    ];

    $this->assertEquals($expectedSubclassTypes, Car::getSingleTableTypeMap());
  }

  public function testQueryingOnRoot() {

    (new Car())->save();
    (new Truck())->save();
    (new Truck())->save();
    (new Bike())->save();

    $results = Vehicle::all();

    $this->assertEquals(4, count($results));

    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Car',   $results[0]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Truck', $results[1]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Truck', $results[2]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Bike',  $results[3]);
  }

  public function testQueryingOnChild() {
    (new Car())->save();
    (new Truck())->save();
    (new Truck())->save();
    (new Bike())->save();

    $results = MotorVehicle::all();

    $this->assertEquals(3, count($results));

    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Car',   $results[0]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Truck', $results[1]);
    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Truck', $results[2]);
  }

  public function testQueryingOnLeaf() {
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

    $moto = new MotorVehicle();
    $moto->save();
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

    $this->assertEquals(1, count(Vehicle::all()));
  }
} 