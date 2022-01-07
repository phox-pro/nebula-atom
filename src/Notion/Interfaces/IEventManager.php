<?php

namespace Phox\Nebula\Atom\Notion\Interfaces;

interface IEventManager
{
    /**
     * Init all events in object by DIContainer
     *
     * @param object $eventsOwner
     * @return void
     */
    public function initObjectEvents(object $eventsOwner): void;

    public function notify(IEvent $event): void;
}