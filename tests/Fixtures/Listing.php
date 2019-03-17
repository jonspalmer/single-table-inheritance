<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Listing extends Eloquent {

  public function vehicles()
  {
    return $this->belongsToMany('Nanigans\SingleTableInheritance\Tests\Fixtures\Vehicle');
  }
}
