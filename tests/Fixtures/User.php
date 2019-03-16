<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;
use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent {

  public function vehicles() {
    return $this->hasMany('Nanigans\SingleTableInheritance\Tests\Fixtures\Vehicle', 'owner_id');
  }
} 