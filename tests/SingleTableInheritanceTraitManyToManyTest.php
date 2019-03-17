<?php

namespace Nanigans\SingleTableInheritance\Tests;

use Nanigans\SingleTableInheritance\Tests\Fixtures\Listing;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Bike;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Car;

/**
 * Class SingleTableInheritanceTraitManyToManyTest
 *
 * A set of tests around many-to-many relationships involving SingleTableInheritanceTrait
 *
 * @package Nanigans\SingleTableInheritance\Tests
 */
class SingleTableInheritanceTraitManyToManyTest extends TestCase {

  protected $redCar;
  protected $blueBike;
  protected $listing;

  public function setUp() {
    parent::setUp();
    $this->redCar = new Car();
    $this->redCar->color = 'red';
    $this->redCar->save();

    $this->blueBike = new Bike();
    $this->blueBike->color = 'blue';
    $this->blueBike->save();

    $this->listing = new Listing();
    $this->listing->name = 'best vehicles 2019';
    $this->listing->save();
    $this->listing->vehicles()->save($this->redCar);
    $this->listing->vehicles()->save($this->blueBike);
  }

  public function testManyToManyCollection() {
    $vehicles = Listing::first()->vehicles;
    $this->assertEquals([$this->redCar->id, $this->blueBike->id], [$vehicles[0]->id, $vehicles[1]->id]);
  }

  public function testManyToManyLoadedCollection() {
    $vehicles = Listing::first()->load('vehicles')->vehicles;
    $this->assertEquals([$this->redCar->id, $this->blueBike->id], [$vehicles[0]->id, $vehicles[1]->id]);
  }

  public function testManyToManyWithCollection() {
    $vehicles = Listing::with('vehicles')->first()->vehicles;
    $this->assertEquals([$this->redCar->id, $this->blueBike->id], [$vehicles[0]->id, $vehicles[1]->id]);
  }
}