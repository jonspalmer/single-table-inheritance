<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

class Audio extends File {

    protected static $singleTableType = 'audio';

    protected static $singleTableSubclasses = [
        'Nanigans\SingleTableInheritance\Tests\Fixtures\AudioMP3',
    ];

}