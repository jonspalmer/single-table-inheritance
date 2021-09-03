<?php


namespace Nanigans\SingleTableInheritance\Tests\Fixtures;


class Book extends Publication
{
    protected static $singleTableType = 'book';
}
