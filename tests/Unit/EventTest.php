<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\Implementation\Event\Event;
use Phox\Nebula\Atom\TestCase;
use stdClass;

class EventTest extends TestCase
{
    public function testBasicEvent(): void
    {
        $eventObject = new class extends Event {};
        $mock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['notify'])
            ->getMock();
        $mock->expects($this->once())->method('notify');

        $eventObject::listen([$mock, 'notify']);
        $eventObject->notify();
    }

    public function testOneListenerCallsByClass(): void
    {
        $firstEvent = new class extends Event {};
        $secondEvent = new class extends Event {};

        $mock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['notify'])
            ->getMock();
        $mock->expects($this->once())->method('notify');

        $firstEvent::listen([$mock, 'notify']);
        $secondEvent::listen([$mock, 'notify']);

        $firstEvent->notify();
    }
}