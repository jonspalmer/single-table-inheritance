<?php

namespace Nanigans\SingleTableInheritance\Tests;

use Illuminate\Support\Facades\DB;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Bike;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Car;
use Nanigans\SingleTableInheritance\Tests\Fixtures\MotorVehicle;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Vehicle;

use Nanigans\SingleTableInheritance\Tests\Fixtures\Video;
use Nanigans\SingleTableInheritance\Tests\Fixtures\VideoType;
use Nanigans\SingleTableInheritance\Tests\Fixtures\MP4Video;

use Nanigans\SingleTableInheritance\Tests\Fixtures\Fruit;
use Nanigans\SingleTableInheritance\Tests\Fixtures\FruitType;

/**
 * Class SingleTableInheritanceTraitModelMethodsTest
 *
 * A set of tests of the model methods added to by the SingleTableInheritanceTrait
 *
 * @package Nanigans\SingleTableInheritance\Tests
 */
class SingleTableInheritanceTraitModelMethodsTest extends TestCase {

  // getPersistedAttributes

  public function testGetPersistedOfRoot() {
    $attributes = (new Vehicle)->getPersistedAttributes();
    sort($attributes);
    $this->assertEquals(['color', 'created_at', 'id', 'owner_id', 'type', 'updated_at'], $attributes);
  }

  public function testGetPersistedOfChild() {
    $attributes = (new MotorVehicle)->getPersistedAttributes();
    sort($attributes);
    $this->assertEquals(['color', 'created_at', 'fuel', 'id', 'owner_id', 'type', 'updated_at'], $attributes);
  }

  public function testGetPersistedOfLeaf() {
    $attributes = (new Car)->getPersistedAttributes();
    sort($attributes);
    $this->assertEquals(['capacity', 'color', 'created_at', 'fuel', 'id', 'owner_id', 'type', 'updated_at'], $attributes);
  }

  public function testGetPersistedIncludesDates() {
    $vehicle = new Vehicle;
    $vehicle->setDates(['purchase_date']);
    $attributes = $vehicle->getPersistedAttributes();
    sort($attributes);
    $this->assertEquals(['color', 'created_at', 'id', 'owner_id', 'purchase_date', 'type', 'updated_at'], $attributes);
  }

  public function testGetPersistedIncludesPrimaryKey() {
    $vehicle = new Vehicle;
    $vehicle->setPrimaryKey('identifier');
    $attributes = $vehicle->getPersistedAttributes();
    sort($attributes);
    $this->assertEquals(['color', 'created_at', 'identifier', 'owner_id', 'type', 'updated_at'], $attributes);
  }

  public function testGetPersistedIsEmptyIfStaticMappingIsNull() {
    $attributes = Car::withAllPersisted([], function() {
      return (new Car)->getPersistedAttributes();
    });

    $this->assertEquals([], $attributes);
  }

  // getQualifiedSingleTableTypeColumn

  public function testGetQualifiedSingleTableTypeColumn() {
    $car = new Car;
    $car->setTable('cars');
    $tableTypeColumn = Car::withTypeField('discriminator', function() use($car) {
      return $car->getQualifiedSingleTableTypeColumn();
    });
    $this->assertEquals('cars.discriminator', $tableTypeColumn);
  }

  // getSingleTableTypes

  public function testGetSingleTableTypesOfRoot() {
    $types = ['motorvehicle', 'car', 'truck', 'bike'];

    $this->assertEquals($types, (new Vehicle)->getSingleTableTypes());
  }

  public function testGetTypeMapOfChild() {
    $types = ['motorvehicle', 'car', 'truck'];

    $this->assertEquals($types, (new MotorVehicle())->getSingleTableTypes());
  }

  public function testGetTypeMapOfLeaf() {
    $types = ['car'];

    $this->assertEquals($types, (new Car)->getSingleTableTypes());
  }

  // setSingleTableType

  public function testSetSingleTableTypeUsesTypeField() {
    $car = new Car;
    Car::withTypeField('discriminator', function() use($car) {
      $car->setSingleTableType();
    });

    $this->assertEquals('car', $car->getAttributes()['discriminator']);
  }
  
  public function testSetSingleTableTypeWithEnum() {
    $video = new MP4Video();
    $video->setSingleTableType();
  }

  /**
   * @expectedException \Nanigans\SingleTableInheritance\Exceptions\SingleTableInheritanceException
   */
  public function testSetSingleTableTypeThrowExceptionIfTableTypeIsUnset() {
    (new Vehicle)->setSingleTableType();
  }

  // filterPersistedAttributes

  public function testFilterPersistedAttributes() {
    $car = new Car;
    $car->fuel = 'diesel';
    $car->junk = 'trunk';
    $car->wingspan = 30;

    $car->filterPersistedAttributes();

    $this->assertEquals(['fuel' => 'diesel'], $car->getAttributes());
  }

  /**
   * @expectedException \Nanigans\SingleTableInheritance\Exceptions\SingleTableInheritanceInvalidAttributesException
   */
  public function testFilterPersistedAttributesThrowsIfConfigured() {
    $bike = new Bike;
    $bike->fuel = 'diesel';

    $bike->filterPersistedAttributes();
  }

  public function testFilterPersistedAttributesDoesNothingIfPersistedIsEmpty() {
    $car = \Mockery::mock('Nanigans\SingleTableInheritance\Tests\Fixtures\Car')->makePartial();

    $car->shouldReceive('getPersistedAttributes')
      ->once()
      ->andReturn([]);

    $car->fuel = 'diesel';
    $car->junk = 'trunk';
    $car->wingspan = 30;

    $car->filterPersistedAttributes();

    $this->assertEquals(['fuel' => 'diesel','junk' => 'trunk', 'wingspan' => 30], $car->getAttributes());
  }

  // setFilteredAttributes

  public function testSetFilteredAttributes() {
    $car = new Car;
    $car->setFilteredAttributes(['fuel' => 'diesel','junk' => 'trunk', 'wingspan' => 30]);

    $this->assertEquals(['fuel' => 'diesel'], $car->getAttributes());
  }

  /**
   * @expectedException \Nanigans\SingleTableInheritance\Exceptions\SingleTableInheritanceInvalidAttributesException
   */
  public function testSetFilteredAttributeshrowsIfConfigured() {
    $bike = new Bike;
    $bike->setFilteredAttributes(['fuel' => 'diesel']);
  }

  public function testSetFilteredAttributesDoesNothingIfPersistedIsEmpty() {
    $car = \Mockery::mock('Nanigans\SingleTableInheritance\Tests\Fixtures\Car')->makePartial();

    $car->shouldReceive('getPersistedAttributes')
      ->once()
      ->andReturn([]);

    $car->setFilteredAttributes(['fuel' => 'diesel','junk' => 'trunk', 'wingspan' => 30]);

    $this->assertEquals(['fuel' => 'diesel','junk' => 'trunk', 'wingspan' => 30], $car->getAttributes());
  }


  // newFromBuilder

  public function testNewFromBuilder() {
    $vehicle = new Vehicle;
    $attr = new \stdClass();
    $attr->type = 'car';
    $attr->fuel = 'diesel';
    $attr->color = 'red';
    $attr->cruft = 'junk';

    $newVehicle = $vehicle->newFromBuilder($attr);

    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\Car', $newVehicle);
    $this->assertEquals('diesel', $newVehicle->fuel);
    $this->assertEquals('red', $newVehicle->color);
    $this->assertNull($newVehicle->cruft);
  }

  public function testNewFromBuilderWithEnum() {
    $video = new Video;
    $attr = new \stdClass();
    $attr->type = VideoType::MP4;

    $newVideo = $video->newFromBuilder($attr);

    $this->assertInstanceOf('Nanigans\SingleTableInheritance\Tests\Fixtures\MP4Video', $newVideo);
  }
  
  /**
   * @expectedException \Nanigans\SingleTableInheritance\Exceptions\SingleTableInheritanceException
   */
  public function testNewFromBuilderThrowsIfClassTypeIsUndefined() {
    $vehicle = new Vehicle;
    $attr = new \stdClass();
    $vehicle->newFromBuilder($attr);
  }

  /**
   * @expectedException \Nanigans\SingleTableInheritance\Exceptions\SingleTableInheritanceException
   */
  public function testNewFromBuilderThrowsIfClassTypeIsNull() {
    $vehicle = new Vehicle;
    $attr = new \stdClass();
    $attr->type = null;
    $vehicle->newFromBuilder($attr);
  }

  /**
   * @expectedException \Nanigans\SingleTableInheritance\Exceptions\SingleTableInheritanceException
   */
  public function testNewFromBuilderThrowsIfClassTypeIsUnrecognized() {
    $vehicle = new Vehicle;
    $attr = new \stdClass();
    $attr->type = 'junk';
    $vehicle->newFromBuilder($attr);
  }
  
  
  public function testNumericalTypes() {
    $types = [FruitType::APPLE, FruitType::BANANA]; // integer types
    
    $this->assertEquals($types, (new Fruit())->getSingleTableTypes());
  }
}


