<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\Notion\Interfaces\IEventManager;
use ReflectionNamedType;
use ReflectionObject;
use ReflectionProperty;

class EventManager implements IEventManager
{
    public function __construct(protected IDependencyInjection $dependencyInjection)
    {
        //
    }

    public function initObjectEvents(object $eventsOwner): void
    {
        $reflectionObject = new ReflectionObject($eventsOwner);
        $properties = $reflectionObject->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $type = $property->getType();

            if ($type instanceof ReflectionNamedType && is_subclass_of($class = $type->getName(), IEvent::class)) {
                $property->setValue($eventsOwner, $this->dependencyInjection->make($class));
            }
        }
    }

    public function notify(IEvent $event): void
    {
        $this->dependencyInjection->singleton($event);
        $this->dependencyInjection->singleton($event, IEvent::class);
        
        $listeners = $event->getListeners();

        foreach ($listeners as $listener) {
            $this->dependencyInjection->call($listener);
        }

        $this->dependencyInjection->deleteDependency($event::class);
        $this->dependencyInjection->deleteDependency(IEvent::class);
    }
}