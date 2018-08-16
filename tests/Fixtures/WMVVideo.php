<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

class WMVVideo extends Video {

  protected static $singleTableType = VideoType::WMV;
}