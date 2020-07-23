<?php

namespace Nanigans\SingleTableInheritance\Tests\Fixtures;

/**
 * AudioMP3 is a Class for WrongInheritanceException test.
 * AudioMP3 should extend Audio, but it extends File.
 */
class AudioMP3 extends File {

    protected static $singleTableType = 'audio/mp3';

}