<?php


namespace Nanigans\SingleTableInheritance\Tests;

use Nanigans\SingleTableInheritance\Tests\Fixtures\Book;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Publication;
use Nanigans\SingleTableInheritance\Tests\Fixtures\Publisher;

class SingleTableInheritanceMutatedPropertyTest extends TestCase
{

    public function testMutatedPropertyDirect() {
        $publisher_attr = ['name' => 'MyPublishingHouse'];
        $publisher = new Publisher($publisher_attr);
        $publisher->save();

        $publication_attr = ['name' => 'MyBook', 'publisher_id' => $publisher->id, 'type' => 'book'];
        $book = new Book($publication_attr);
        $book->save();
        $publisher_id = $book->getAttributeValue('publisher_id');

        $expected = $publisher->id . "test";

        $this->assertEquals($expected, $publisher_id);

    }

    public function testMutatedPropertyFromBuilder() {
        $publisher_attr = ['name' => 'MyPublishingHouse'];
        $publisher = new Publisher($publisher_attr);
        $publisher->save();

        $publication_attr = ['name' => 'MyBook', 'publisher_id' => $publisher->id, 'type' => 'book'];
        $parent = new Publication;
        $book = $parent->newFromBuilder($publication_attr);
        $book->save();
        $publisher_id = $book->getAttributeValue('publisher_id');
        $expected = $publisher->id . "test";


        $this->assertEquals($expected, $publisher_id);

    }

}
