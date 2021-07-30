<?php

namespace Tests\Unit;

use ArrayAccess;
use Countable;
use Iterator;
use Phox\Nebula\Atom\Implementation\Basics\Collection;
use Phox\Nebula\Atom\Implementation\Exceptions\BadCollectionType;
use Phox\Nebula\Atom\Implementation\Exceptions\CollectionHasKey;
use Phox\Nebula\Atom\Implementation\States\InitState;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\TestCase;

class CollectionTest extends TestCase 
{
    public function testCollectionTypes(): void
    {
        $collection = new Collection('string');
        $this->assertEquals('string', $collection->getType());
    }

    public function testCollectionList(): void
    {
        $collection = new Collection('integer');
        $this->assertEquals([], $collection->all());
    }

    /**
     * @throws BadCollectionType
     */
    public function testAddItems(): void
    {
        $collection = new Collection('integer');

        $collection->add(1);

        $this->assertEquals([1], $collection->all());

        $collection->add(52);

        $this->assertEquals([1, 52], $collection->all());
    }

    /**
     * @throws CollectionHasKey
     * @throws BadCollectionType
     */
    public function testSetItems(): void
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
     * @throws BadCollectionType
     */
    public function testCollectionFirstItem(): void
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
     * @throws BadCollectionType
     */
    public function testDeleteItems(): void
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
     * @throws BadCollectionType
     */
    public function testCollectMethod(): void
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
     * @throws BadCollectionType
     */
    public function testEmptyMethod(): void
    {
        $collection = new Collection('integer');

        $this->assertTrue($collection->empty());

        $collection->add(1);
        $this->assertFalse($collection->empty());

        $collection->delete(1);
        $this->assertTrue($collection->empty());
    }

    /**
     * @throws BadCollectionType
     */
    public function testClearMethod(): void
    {
        $collection = new Collection('integer');

        $collection->collect(1, 2, 3);
        $this->assertFalse($collection->empty());

        $collection->clear();
        $this->assertTrue($collection->empty());
    }

    /**
     * @throws CollectionHasKey
     * @throws BadCollectionType
     */
    public function testGetAllKeys(): void
    {
        $collection = new Collection('integer');

        $collection->add(5);
        $collection->set(4, 5);

        $this->assertEquals([0, 4], $collection->keys());
    }

    /**
     * @throws BadCollectionType
     */
    public function testSearchByValue(): void
    {
        $collection = new Collection('integer');

        $collection->collect(1, 2, 3, 4);
        $this->assertEquals(2, $collection->search(3));
    }

    /**
     * @throws BadCollectionType
     */
    public function testHasMethod(): void
    {
        $collection = new Collection('integer');

        $collection->collect(1, 2, 3);

        $this->assertTrue($collection->has(2));
        $this->assertFalse($collection->has(4));
    }

    /**
     * @throws BadCollectionType
     */
    public function testGetMethod(): void
    {
        $collection = new Collection('integer');

        $collection->collect(1, 2, 3);

        $this->assertEquals(2, $collection->get(1));
        $this->assertEquals(3, $collection[2]); 
    }

    /**
     * @throws BadCollectionType
     */
    public function testMergeMethod(): void
    {
        $collection = new Collection('integer');

        $collection->add(1);
        $collection->add(2);
        $collection->merge([3, 4]);

        $this->assertEquals([1, 2, 3, 4], $collection->all());
    }

    public function testWrongTypeBasic(): void
    {
        $this->expectException(BadCollectionType::class);
        (new Collection('integer'))->add('Test');
    }

    public function testWrongTypeCallable(): void
    {
        $this->expectException(BadCollectionType::class);
        (new Collection('callable'))->add(123);
    }

    public function testWrongTypeArray(): void
    {
        $this->expectException(BadCollectionType::class);
        (new Collection('array'))->add(new \stdClass);
    }

    /**
     * @throws BadCollectionType
     */
    public function testExtendedObjectAllowed(): void
    {
        $collection = new Collection(IEvent::class);

        $collection->add(new InitState());

        $item = $collection->first();

        $this->assertInstanceOf(IEvent::class, $item);
        $this->assertInstanceOf(InitState::class, $item);
    }

    /**
     * @throws BadCollectionType
     */
    public function testAllObjectAllowed(): void
    {
        $collection = new Collection('object');

        $collection->add(new InitState());

        $item = $collection->first();

        $this->assertInstanceOf(IEvent::class, $item);
        $this->assertInstanceOf(InitState::class, $item);
    }
}