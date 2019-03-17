<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Video extends Eloquent {

  use SingleTableInheritanceTrait;

  protected $table = "videos";

  protected static $singleTableTypeField = 'type';

  protected static $singleTableSubclasses = [
    'Nanigans\SingleTableInheritance\Tests\Fixtures\MP4Video',
    'Nanigans\SingleTableInheritance\Tests\Fixtures\WMVVideo',
  ];
}

class VideoType{
  const MP4 = 1;
  const WMV = 2;
}