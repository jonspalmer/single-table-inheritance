<?php

namespace Nanigans\SingleTableInheritance\Tests;

use Illuminate\Support\Facades\DB;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Bike;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Car;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Taxi;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Vehicle;
use Nanigans\SingleTableInheritance\Tests\Fixtures\User;

/**
 * Class SingleTableInheritanceTraitPersistenceTest
 *
 * A set of tests of the persistence methods added to by the SingleTableInheritanceTrait
 * These tests are mostly duplicative of the model and static tests but they prove the integration
 * of the Trait with key parts of the Eloquent ORM.
 *
 * @package Nanigans\SingleTableInheritance\Tests
 */
class SingleTableInheritanceTraitPersistenceTest extends TestCase {

  /**
   * @expectedException \Nanigans\SingleTableInheritance\Exceptions\SingleTableInheritanceException
   */
  public function testSavingThrowsExceptionIfModelHasNoClassType() {
    (new Vehicle())->save();
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

  public function testSaveAttributesWhereSingleTableInheritanceTypeHasAnAccessor(){
    $taxi = new Taxi;
    $taxi->color = 'yellow';
    $taxi->fuel = 'diesel';
    $taxi->cruft = 'yellow is my favorite';
    $taxi->save();

    $dbTaxi = DB::table('vehicles')->first();

    $this->assertEquals($taxi->id, $dbTaxi->id);

    $this->assertEquals('yellow', $dbTaxi->color);
    $this->assertEquals('diesel', $dbTaxi->fuel);

    $this->assertEquals('Taxi', $dbTaxi->type);
  }

  /**
   * @expectedException \Nanigans\SingleTableInheritance\Exceptions\SingleTableInheritanceException
   */
  public function testSaveThrowsExceptionForInvalidAttributesIfConfigured() {
    $bike = new Bike;
    $bike->color = 'red';
    $bike->cruft = 'red is my favorite';
    $bike->save();
  }
}