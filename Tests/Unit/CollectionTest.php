<?php

namespace Tests\Unit;

use ArrayAccess;
use Countable;
use Iterator;
use Phox\Nebula\Atom\Implementation\Basics\Collection;
use Phox\Nebula\Atom\Implementation\Exceptions\BadCollectionType;
use Phox\Nebula\Atom\Implementation\Exceptions\CollectionHasKey;
use Phox\Nebula\Atom\TestCase;

class CollectionTest extends TestCase 
{
    /**
     * @test
     */
    public function defaultInterfaces()
    {
        foreach ([
            Iterator::class,
            Countable::class,
            ArrayAccess::class
        ] as $interface) {
            $this->assertTrue(is_subclass_of(Collection::class, $interface));
        }
    }

    /**
     * @test
     */
    public function collectionTypes()
    {
        $collection = new Collection('string');
        $this->assertEquals('string', $collection->getType());
    }

    /**
     * @test
     */
    public function collectionList()
    {
        $collection = new Collection('integer');
        $this->assertEquals([], $collection->all());
    }

    /**
     * @test
     */
    public function addItems()
    {
        $collection = new Collection('integer');
        $collection->add(1);
        $this->assertEquals([1], $collection->all());
        $collection->add(52);
        $this->assertEquals([1, 52], $collection->all());
    }

    /**
     * @test
     */
    public function setItems()
    {
        $collection = new Collection(gettype(1.5));
        $collection->set(5, (float)1);
        $this->assertSame([5 => 1.0], $collection->all());
        $collection->replace(5, 2.0);
        $this->assertSame([5 => (double)2], $collection->all());
        $this->expectException(CollectionHasKey::class);
        $collection->set(5, 3.0);
    }

    /**
     * @test
     */
    public function collectionFirstItem()
    {
        $collection = new Collection(gettype(1));
        $collection->add(3);
        $collection->add(5);
        $this->assertEquals(3, $collection->first());
        $collection->add(4);
        $collection->delete(3);
        $this->assertEquals(5, $collection->first());
    }

    /**
     * @test
     */
    public function deleteItems()
    {
        $collection = new Collection('string');
        $collection->add('Hello');
        $collection->add('World');
        $collection->add('More');
        $collection->deleteByIndex(1);
        $this->assertEquals([0 => 'Hello', 2 => 'More'], $collection->all());
        $collection->delete('Hello');
        $this->assertEquals([2 => 'More'], $collection->all());
    }

    /**
     * @test
     */
    public function collectTest()
    {
        $collection = new Collection('integer');
        $collection->collect(1, 2, 3);
        $this->assertEquals([1, 2, 3], $collection->all());
        $collection->collect(1, [2, 3, 4]);
        $this->assertEquals([1, 2, 3, 4], $collection->all());
        $collection = new Collection('array');
        $collection->collect([[1]], [[2, 3, 4]]);
        $this->assertEquals([[1], [2, 3, 4]], $collection->all());
    }

    /**
     * @test
     */
    public function emptyTest()
    {
        $collection = new Collection('integer');
        $this->assertTrue($collection->empty());
        $collection->add(1);
        $this->assertFalse($collection->empty());
        $collection->delete(1);
        $this->assertTrue($collection->empty());
    }

    /**
     * @test
     */
    public function clearTest()
    {
        $collection = new Collection('integer');
        $collection->collect(1, 2, 3);
        $this->assertFalse($collection->empty());
        $collection->clear();
        $this->assertTrue($collection->empty());
    }

    /**
     * @test
     */
    public function getAllKeys()
    {
        $collection = new Collection('integer');
        $collection->add(5);
        $collection->set(4, 5);
        $this->assertEquals([0, 4], $collection->keys());
    }

    /**
     * @test
     */
    public function searchByValue()
    {
        $collection = new Collection('integer');
        $collection->collect(1, 2, 3, 4);
        $this->assertEquals(2, $collection->search(3));
    }

    /**
     * @test
     */
    public function hasMethodTest()
    {
        $collection = new Collection('integer');
        $collection->collect(1, 2, 3);
        $this->assertTrue($collection->has(2));
        $this->assertFalse($collection->has(4));
    }

    /**
     * @test
     */
    public function getTest()
    {
        $collection = new Collection('integer');
        $collection->collect(1, 2, 3);
        $this->assertEquals(2, $collection->get(1));
        $this->assertEquals(3, $collection[2]); 
    }

    /**
     * @test
     */
    public function mergeTest()
    {
        $collection = new Collection('integer');
        $collection->add(1);
        $collection->add(2);
        $collection->merge([3, 4]);
        $this->assertEquals([1, 2, 3, 4], $collection->all());
    }

    /**
     * @test
     */
    public function wrongTypeBasic()
    {
        $this->expectException(BadCollectionType::class);
        $this->expectExceptionMessageRegExp("/^.+'integer'.+'string'.+$/u");
        (new Collection('integer'))->add('Test');
    }

    /**
     * @test
     */
    public function wrongTypeCallable()
    {
        $this->expectException(BadCollectionType::class);
        $this->expectExceptionMessageRegExp("/^.+'callable'.+'integer'.+$/u");
        (new Collection('callable'))->add(123);
    }

    /**
     * @test
     */
    public function wrongTypeObject()
    {
        $this->expectException(BadCollectionType::class);
        $this->expectExceptionMessageRegExp("/^.+'array'.+'stdClass'.+$/u");
        (new Collection('array'))->add(new \stdClass);
    }
}