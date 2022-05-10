<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

use Illuminate\Database\Eloquent\Builder;
use Nanigans\SingleTableInheritance\SingleInheritanceBuilderTrait;

class VehicleBuilder extends Builder
{
   use SingleInheritanceBuilderTrait;

   protected $singleInheritanceRootModelClass = Vehicle::class;
}
