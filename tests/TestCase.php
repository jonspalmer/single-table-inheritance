<?php

namespace Phaza\SingleTableInheritance\Tests;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use \Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase {

  /**
   * Setup the test environment.
   */
  public function setUp() {
    parent::setUp();

    // migrations only for testing purpose
    \Artisan::call('migrate', array(
      			'--path' => '../tests/migrations',
      			'--database' => 'testbench',
      		));

    // Laravel is dumb. It calls boot only for the first test but wipes out the observers for others
    // So we call boot ourselves to make event observing work.

    // On the first test, we've already booted, so clear out the listeners. If we haven't booted
    // already, this is a no-op.
    // https://github.com/laravel/framework/issues/1181#issuecomment-51627220

    $models = [
      'Phaza\SingleTableInheritance\Tests\Fixtures\Vehicle',
      'Phaza\SingleTableInheritance\Tests\Fixtures\MotorVehicle',
      'Phaza\SingleTableInheritance\Tests\Fixtures\Car',
      'Phaza\SingleTableInheritance\Tests\Fixtures\Truck',
      'Phaza\SingleTableInheritance\Tests\Fixtures\Bike'
    ];
    // Reset each model event listeners.
    foreach ($models as $model) {

      // Flush any existing listeners.
      call_user_func(array($model, 'flushEventListeners'));

      // Reregister them.
      call_user_func(array($model, 'boot'));
    }
  }

  /**
   * Define environment setup.
   *
   * @param  Illuminate\Foundation\Application $app
   * @return void
   */
  protected function getEnvironmentSetUp($app) {
    // reset base path to point to our package's src directory
    $app['path.base'] = __DIR__ . '/../src';

    $app['config']->set('database.default', 'testbench');
    $app['config']->set('database.connections.testbench', array (
      'driver'   => 'sqlite',
      'database' => ':memory:',
      'prefix'   => '',
    ));
  }
}
