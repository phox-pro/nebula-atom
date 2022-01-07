<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\Implementation\BasicEvent;
use Phox\Nebula\Atom\Notion\Interfaces\IEventManager;
use Phox\Nebula\Atom\TestCase;
use stdClass;

class EventsTest extends TestCase 
{
    public function testEventAddListeners(): void
    {
        $event = $this->container()->make(BasicEvent::class);
        $mock = $this->getMockBuilder(stdClass::class)->addMethods(['test'])->getMock();
        $mock->expects($this->once())->method('test');

        /** @var callable $listener */
        $listener = [$mock, 'test'];

        $this->assertTrue($event->getListeners()->isEmpty());

        $event->listen($listener);

        $this->assertFalse($event->getListeners()->isEmpty());
        $this->assertEquals(1, $event->getListeners()->count());
        $this->assertEquals($listener, $event->getListeners()->first());

        $eventManager = $this->container()->get(IEventManager::class);
        $eventManager->notify($event);
    }
}