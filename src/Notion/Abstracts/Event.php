<?php

namespace Phox\Nebula\Atom\Notion\Abstracts;

use Phox\Nebula\Atom\Implementation\Functions;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Structures\Collection;

abstract class Event implements IEvent
{
    /** @var Collection<callable> */
    protected Collection $listeners;

    public function __construct()
    {
        $this->listeners = new Collection('callable');
    }

    public function listen(callable $listener): void
    {
        if (!$this->listeners->contains($listener)) {
            $this->listeners->add($listener);
        }
    }

    public function notify(...$params): void
    {
        $this->notifyRaw($params);
    }

    public function notifyRaw(array $params = []): void
    {
        Functions::container()->singleton($this, IEvent::class);

        foreach ($this->listeners as $listener) {
            Functions::container()->call($listener, $params);
        }

        Functions::container()->deleteDependency(IEvent::class);
    }

    public function getListeners() : Collection
    {
        return $this->listeners;
    }
}