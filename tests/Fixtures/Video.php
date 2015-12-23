<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;
use SebastianBergmann\Exporter\Exception;

class Video extends Eloquent {

  use SingleTableInheritanceTrait;

  protected $table = "videos";

  protected static $singleTableTypeField = 'type';

  protected static $singleTableSubclasses = [
    'Nanigans\SingleTableInheritance\Tests\Fixtures\MP4Video'
  ];
}

class VideoType{
    const MP4 = 0;
    const WMV = 1;
}