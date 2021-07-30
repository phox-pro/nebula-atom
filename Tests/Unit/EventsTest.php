<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\Implementation\BasicEvent;
use Phox\Nebula\Atom\Implementation\Functions;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\TestCase;
use stdClass;

class EventsTest extends TestCase 
{
    public function testEventAddListeners(): void
    {
        $event = new BasicEvent();
        $mock = $this->getMockBuilder(stdClass::class)->addMethods(['test'])->getMock();
        $mock->expects($this->once())->method('test');

        /** @var callable $listener */
        $listener = [$mock, 'test'];

        $this->assertTrue($event->getListeners()->empty());

        $event->listen($listener);

        $this->assertFalse($event->getListeners()->empty());
        $this->assertEquals(1, $event->getListeners()->count());
        $this->assertEquals($listener, $event->getListeners()->first());

        $event->notify();
    }
}