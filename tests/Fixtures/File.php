<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;

class File extends Eloquent {

    use SingleTableInheritanceTrait;

    protected $table = "audios";

    protected static $singleTableTypeField = 'type';

    protected static $singleTableSubclasses = [
        'Nanigans\SingleTableInheritance\Tests\Fixtures\Audio',
    ];
}