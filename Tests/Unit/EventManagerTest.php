<?php

use Phox\Nebula\Atom\Implementation\BasicEvent;
use Phox\Nebula\Atom\Implementation\EventManager;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\Notion\Interfaces\IEventManager;
use Phox\Nebula\Atom\TestCase;

class EventManagerTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testInitObjectEvents(): void
    {
        $dispatcher = $this->container()->get(IEventManager::class);
        $testObject = new class {
            public BasicEvent $event1;
            public BasicEvent $event2;
        };

        $reflectionClass = new ReflectionClass($testObject::class);
        $property1 = $reflectionClass->getProperty('event1');
        $property2 = $reflectionClass->getProperty('event2');

        $this->assertFalse($property1->isInitialized($testObject));
        $this->assertFalse($property2->isInitialized($testObject));

        $dispatcher->initObjectEvents($testObject);

        $this->assertNotNull($testObject->event1);
        $this->assertNotNull($testObject->event2);
        $this->assertInstanceOf(BasicEvent::class, $testObject->event1);
        $this->assertInstanceOf(BasicEvent::class, $testObject->event2);
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(
            EventManager::class,
            $this->container()->get(IEventManager::class)
        );

        $this->assertIsSingleton(IEventManager::class);
    }
}
