<?php

namespace Nanigans\SingleTableInheritance\Tests;

use Illuminate\Support\Facades\DB;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Vehicle;

final class SingleTableInheritanceTraitCreateFromBaseModel extends TestCase
{
  private const EXPECTED_TYPE = 'truck';

  public function testHasCorrectTypeAfterCreation(): void
  {
    Vehicle::create([Vehicle::TYPE_FIELD => self::EXPECTED_TYPE]);
    $actualType = DB::table('vehicles')->first()->{Vehicle::TYPE_FIELD} ?? '';
    self::assertEquals(self::EXPECTED_TYPE, $actualType);
  }
}
